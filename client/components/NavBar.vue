<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container justify-between">
      <NuxtLink class="nav-link" to="/">
        <h2>Nuxt Auth</h2>
      </NuxtLink>
      <div class="" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item" v-if="!$auth.loggedIn">
            <NuxtLink class="nav-link" to="/register">Register</NuxtLink>
          </li>
          <li class="nav-item" v-if="!$auth.loggedIn">
            <NuxtLink class="nav-link" to="/login">Login</NuxtLink>
          </li>
          <li class="nav-item" v-if="$auth.loggedIn">
            <NuxtLink class="nav-link" to="/category">Category</NuxtLink>
          </li>
          <li class="nav-item" v-if="$auth.loggedIn">
            <span @click="logout" class="btn">Logout</span>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  middleware: "auth",
  methods: {
    async logout() {
      this.$nuxt.$loading.start();
      await this.$auth.logout();
      this.$nuxt.$loading.finish();
      this.$router.push({
        path: "/login",
      });
    },
  },
};
</script>
<style></style>
