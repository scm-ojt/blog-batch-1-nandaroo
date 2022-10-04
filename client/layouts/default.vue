<template>
    <div>
      <Navbar class="mb-5"/>
      <main class="w-100"><Nuxt /></main>
    </div>
  </template>
  
  <script>
  import Navbar from "~/components/NavBar";
  
  export default {
    components: {
      Navbar,
    },
  };
  </script>
  
  <style></style>
  