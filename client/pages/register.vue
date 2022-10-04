<template>
  <form class="col-3 m-auto text-center" @submit.prevent="register()">
    <img class="mb-4" src="/favicon.ico" alt="" width="72" height="57" />
    <h1 class="h3 mb-3 fw-normal">Please Sign Up</h1>
    <div class="form-floating mt-2">
      <input
        type="text"
        class="form-control"
        id="floatingName"
        placeholder="Su Su"
        v-model="user.name"
      />
      <label for="floatingName">User Name</label>
    </div>
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
    <div class="form-floating my-2">
      <input
        type="password"
        class="form-control"
        id="floatingPasswordConfirmation"
        placeholder="Password"
        v-model="user.password_confirmation"
      />
      <label for="floatingPasswordConfirmation">Password Confirmation</label>
    </div>
    <button class="w-100 btn btn-lg btn-primary" type="submit">Submit</button>
  </form>
</template>

<script>
export default {
  auth: "guest",
  data() {
    return {
      user: {
        name: null,
        email: null,
        password: null,
        password_confirmation: null,
        _token:''
      },
    };
  },
  mounted() {
    this.$axios.$get("http://127.0.0.1:8000/sanctum/csrf-cookie");
  },
  methods: {
    register() {
      try {
        this.$axios.post("http://127.0.0.1:8000/register", this.user).then((res) => {
          this.$auth.loginWith("laravelSanctum", { data: this.user });
          this.$router.push({
            path: "/category",
          });
        });
      } catch (err) {
        console.log(err.response);
      }
    },
  },
};
</script>

<style></style>
