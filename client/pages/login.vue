<template>
  <form class="col-3 m-auto" @submit.prevent="login()">
    <div class="text-center">
      <img class="mb-3" src="/favicon.ico" alt="" width="80" height="70" />
    </div>
    <h1 class="h3 mb-3 fw-normal text-center">Please Login In</h1>

    <div class="form-floating my-2">
      <input
        type="email"
        class="form-control"
        id="floatingInput"
        placeholder="name@example.com"
        v-model="user.email"
      />
      <label for="floatingInput">Email address</label>
      <small class="text-danger" v-if="errors.email != null">* {{ errors.email[0] }}</small>
    </div>
    <div class="form-floating my-2">
      <input
        type="password"
        class="form-control"
        id="floatingPassword"
        placeholder="Password"
        v-model="user.password"
      />
      <label for="floatingPassword">Password</label>
      <small class="text-danger" v-if="errors.password != null">* {{ errors.password[0] }}</small>
    </div>
    <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
  </form>
</template>

<script>
export default {
  auth: "guest",
  head: {
    title: "Login"
  },
  data() {
    return {
      user: {
        email: null,
        password: null,
      },
      errors: {},
    };
  },
  mounted() {
    this.$axios.$get("http://localhost:8000/sanctum/csrf-cookie");
  },
  methods: {
    async login() {
      this.$nuxt.$loading.start();
      try {
        await this.$auth.loginWith("laravelSanctum", { data: this.user });
        await this.$router.push({
          path: "/category",
        });
      } catch (err) {
        this.errors = err.response.data.errors;
      }
      this.$nuxt.$loading.finish();
    },
  },
};
</script>

<style></style>
