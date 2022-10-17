<template>
  <div class="content">
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
.content {
  min-height: 100vh;
  height: auto;
  width:100%;
}
</style>
