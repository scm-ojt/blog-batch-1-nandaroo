<template>
  <form class="col-3 m-auto text-center" @submit.prevent="login()">
    <img class="mb-4" src="/favicon.ico" alt="" width="72" height="57" />
    <h1 class="h3 mb-3 fw-normal">Please Login In</h1>

    <div class="form-floating my-2">
      <input
        type="email"
        class="form-control"
        id="floatingInput"
        placeholder="name@example.com"
        v-model="user.email"
      />
      <label for="floatingInput">Email address</label>
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
    </div>
    <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
  </form>
</template>

<script>
export default {
  auth: "guest",
  data() {
    return {
      user: {
        email: null,
        password: null,
      },
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
        console.log(err.response);
      }
      this.$nuxt.$loading.finish();
    },
  },
};
</script>

<style></style>
