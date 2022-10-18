<template>
  <div class="content min-vh-100 bg-light">
    <Navbar />
    <main class="p-5 bg-light"><Nuxt /></main>
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

<style>
</style>
