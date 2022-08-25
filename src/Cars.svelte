<script>
    import { onMount, tick } from "svelte";
    import QrCode from "svelte-qrcode"

    import { storedId, storedLogin } from "./stores"

    let userName = sessionStorage.getItem('login')
    
    storedLogin.subscribe(value=>{
      userName = value
    })

    const filterEvent = new Event("filter")

    import Header from "./Header.svelte";
    import Car from "./Car.svelte"
    import BigCar from "./BigCar.svelte"
    let cars = []
    let filteredCars = []
    let brands = []
    let openedCar = false
    let filters = {
      brand: null,
      model: "",
      minYear: null,
      maxYear: null,
      minPrice: null,
      maxPrice: null,
      available: null,
    }
    let year = {min:0, max:0}
    let price = {min:0, max:0}


    async function filter(){
      window.dispatchEvent(filterEvent)
      if(filters.maxYear > year.max && filters.maxYear != null){filters.maxYear = year.max}
      else if(filters.maxYear < year.min && filters.maxYear != null){filters.maxYear = year.min}
      if(filters.minYear > year.max && filters.minYear != null){filters.minYear = year.max}
      else if(filters.minYear < year.min && filters.minYear != null){filters.minYear = year.min}
      if(filters.maxPrice > price.max && filters.maxPrice != null){filters.maxPrice = price.max}
      else if(filters.maxPrice < price.min && filters.maxPrice != null){filters.maxPrice = price.min}
      if(filters.minPrice > price.max && filters.minPrice != null){filters.minPrice = price.max}
      else if(filters.minPrice < price.min && filters.minPrice != null){filters.minPrice = price.min}

      if(filters.maxPrice < filters.minPrice && filters.maxPrice != null && this.id=="priceF"){filters.maxPrice = null}
      else if(filters.maxPrice < filters.minPrice && filters.maxPrice != null && this.id=="priceT"){filters.minPrice = null}
      else if(filters.maxYear < filters.minYear && filters.maxYear != null && this.id=="yearF"){filters.maxYear = null}
      else if(filters.maxYear < filters.minYear && filters.maxYear != null && this.id=="yearT"){filters.minYear = null}
      
      filteredCars = cars.filter(car => {
        if(car.brand==(filters.brand==null?car.brand:filters.brand)){
          if(car.model.toLowerCase().includes(filters.model.toLowerCase())){
            if(car.year>=(filters.minYear==null?car.year:filters.minYear)){
              if(car.year<=(filters.maxYear==null?car.year:filters.maxYear)){
                if(car.price>=(filters.minPrice==null?car.price:filters.minPrice)){
                  if(car.price<=(filters.maxPrice==null?car.price:filters.maxPrice)){
                    if(filters.available===null){
                      return car
                    }
                    else if(filters.available==true && car.available==true){
                      return car
                    }
                    else{
                      if(car.uStatus==filters.available || (car.uStatus=='o' && filters.available=='a')){
                        return car
                      }
                    }
                  }
                }
              }
            }
          }
        }
      })
      filteredCars.sort(sort)
      filteredCars = filteredCars
      brands = []
      filteredCars.forEach(el => {
        if(brands.indexOf(el.brand)==-1){
          brands.push(el.brand)
        }
        if(el.year < year.min || year.min == 0){
          year.min = el.year
        }
        if(el.year > year.max){
          year.max = el.year
        }
        if(el.price < price.min || price.min == 0){
          price.min = el.price
        }
        if(el.price > price.max){
          price.max = el.price
        }
      });
      brands = brands
    }
    function filterBy(e){
      if(e.detail.category == "year"){
        filters.minYear = e.detail.filter
        filters.maxYear = e.detail.filter
      }
      else if(e.detail.category == "price"){
        filters.minPrice = e.detail.filter
        filters.maxPrice = e.detail.filter
      }
      else if(e.detail.category == "brand"){
        filters.brand = e.detail.filter
      }
      else{
        filters.model = e.detail.filter
      }
      filter()
    }

    let sortby = "brand"
    let sortDirection = 1
    function sort(a, b){
      if(sortby=="price" || sortby=='year'){
        return (a[sortby]-b[sortby])*sortDirection
      }
      else{
        return a[sortby].localeCompare(b[sortby])*sortDirection
      }
    }


    let getReservations = async ()=>{
      userName = sessionStorage.getItem('login')
      cars.forEach(el=>{
        el.reservations = []
        el.uStatus = false
      })
      let reservationsFetchRes = await fetch("http://localhost/Svelte/Svelte/public/backend/getReservations.php")
      let reservationsFetchList = await reservationsFetchRes.json()
      reservationsFetchList.forEach(reservation=>{
        let i = cars.findIndex(car=>car.id==reservation.car)
        cars[i].reservations.push(reservation)
        if((userName==null?"":userName).toLowerCase() == reservation.user.toLowerCase()){
          cars[i].uStatus = reservation.status
        }
      })
      cars = cars
    }
    window.addEventListener('login', getReservations)
    window.addEventListener('reserve', getReservations)
    
    onMount(async ()=> {
      let carFetchRes = await fetch("http://localhost/Svelte/Svelte/public/backend/getCars.php")
      let carFetchList = await carFetchRes.json()
      carFetchList.forEach(el => {
        el.reservations = []
        el.uStatus = false
        if(brands.indexOf(el.brand)==-1){
          brands.push(el.brand)
        }
        if(el.year < year.min || year.min == 0){
        year.min = el.year
        }
        if(el.year > year.max){
          year.max = el.year
        }
        if(el.price < price.min || price.min == 0){
          price.min = el.price
        }
        if(el.price > price.max){
          price.max = el.price
        }
      });
      brands = brands
      cars = carFetchList

      let reservationsFetchRes = await fetch("http://localhost/Svelte/Svelte/public/backend/getReservations.php")
      let reservationsFetchList = await reservationsFetchRes.json()
      reservationsFetchList.forEach(reservation=>{
        let i = cars.findIndex(car=>car.id==reservation.car)
        cars[i].reservations.push(reservation)
        if((userName==null?"":userName).toLowerCase() == reservation.user.toLowerCase()){
          cars[i].uStatus = reservation.status
        }
      })

      cars.sort(sort)
      filteredCars = cars
      
    })

    async function openCar(e){
      let car = cars.find(car => car.id == e.detail.id)
      openedCar = car
    }

    let code = false
</script>

<Header/>

<section class="text-gray-600 body-font">
  {#await filteredCars}
    loading
  {:then cars}
    <div class=" flex flex-col md:flex-row container px-5 pt-10 mx-auto">
      <div class=" md:w-1/6 w-full md:my-0 my-1 flex flex-col">
        <select name="brands" id="brands" class=" border-gray-600 rounded-none" bind:value={filters.brand} on:change={filter}>
          <option value={null}>Wszystkie Marki</option>
          {#each brands as brand}
            <option value={brand}>{brand}</option>
          {/each}
        </select>
        <select name="available" id="available" class=" border-gray-600 rounded-none" bind:value={filters.available} on:change={filter}>
          <option value={null}>Wszystkie Samochody</option>
          <option value={true}>Dostępne</option>
          <option value={'p'}>Oczekujące</option>
          <option value={'a'}>Wynajęte</option>
        </select>
      </div>
      <input type="text" name="model" id="model" bind:value={filters.model} on:change={filter} placeholder="Model" class=" md:w-1/6 w-full md:my-0 my-1 border-gray-600 rounded-none box-border">
      <div class=" md:w-1/6 w-full md:my-0 my-1 flex flex-col">
        <input type="number" name="yearF" id="yearF" class=" border-gray-600 rounded-none box-border" bind:value={filters.minYear} on:change={filter} min={year.min} max={year.max} placeholder="Rok od">
        <input type="number" name="yearT" id="yearT" class=" border-gray-600 rounded-none box-border" bind:value={filters.maxYear} on:change={filter} min={year.min} max={year.max} placeholder="Rok do">
      </div>
      <div class=" md:w-1/6 w-full md:my-0 my-1 flex flex-col">
        <input type="number" name="priceF" id="priceF" class=" border-gray-600 rounded-none box-border" bind:value={filters.minPrice} on:change={filter} min={price.min} max={price.max} placeholder="Cena od">
        <input type="number" name="priceT" id="priceT" class=" border-gray-600 rounded-none box-border" bind:value={filters.maxPrice} on:change={filter} min={price.min} max={price.max} placeholder="Cena do">
      </div>
      <div class=" md:w-1/6 w-full md:my-0 my-1 flex flex-col">
        <select  name="sort-category" class=" border-gray-600 rounded-none box-border" bind:value={sortby} on:change={()=>{filteredCars = filteredCars.sort(sort)}}>
          <option value={'brand'}>Marka</option>
          <option value={'model'}>Model</option>
          <option value={'price'}>Cena</option>
          <option value={'year'}>Rocznik</option>
        </select>
        <select  name="sort-direction" class=" border-gray-600 rounded-none box-border" bind:value={sortDirection} on:change={()=>{filteredCars = filteredCars.sort(sort)}}>
          <option value={1}>Rosnąco</option>
          <option value={-1}>Malejąco</option>
        </select>
      </div>
    </div>
    <div class="container px-5 py-10 mx-auto">
      <div class="flex flex-wrap sm:-m-4 -mx-4 -mb-10 -mt-4">
        {#each cars as car}
          <Car on:code={(e)=>{code = e.detail.code}} on:openCar={(e)=>{openCar(e)}} on:filterBy={filterBy} car={car}/>
        {/each}
      </div>
    </div>
  {/await}
</section>

{#if openedCar}
  <BigCar on:closeBigCar={()=>{openedCar=false}} car={openedCar}/>
{/if}

{#if code}
  <div on:click={()=>{code=false}} class=" fixed flex flex-col justify-center items-center p-5 top-1/4 md:left-1/4 left-0 md:w-1/2 w-full h-1/2 z-50 bg-gray-600 text-white rounded-lg">
    <QrCode value={code}/>
  </div>
{/if}