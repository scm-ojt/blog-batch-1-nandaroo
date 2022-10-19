<template>
  <div class="container">
    <div class="row d-flex justify-content-center">
      <div class="col-10">
        <div class="card shadow-lg">
          <div class="card-header">
            <h4 class="fw-bolder text-center text-uppercase">Post Edit</h4>
          </div>
          <div class="card-body">
            <div class="row h-auto my-3">
              <div class="post-create" id="img-frame">
                <img
                  :src="`http://localhost:8000/storage/img/posts/${item.image}`"
                  class="rounded detail-img"
                  alt="data.item.id"
                  v-for="(item, index) in post.images"
                  :key="index"
                />
              </div>
            </div>
            <div class="row">
              <form @submit.prevent="editPost()" id="form">
                <div class="mb-3">
                  <label for="formFile" class="form-label fw-semibold"
                    >Choose Image</label
                  >
                  <input
                    class="form-control"
                    type="file"
                    name="image[]"
                    id="image"
                    multiple="multiple"
                    @change="imageChangeHandler"
                  />
                  <small class="text-danger" v-if="errors['image.0'] != null"
                    >*{{ errors['image.0'][0] }}</small
                  ><small class="text-danger" v-if="errors.image != null"
                    >*{{ errors.image }}</small
                  >
                </div>

                <div class="form-group mb-3">
                  <label for="name" class="fw-semibold">Select Category</label>
                  <select
                    class="form-select"
                    multiple
                    name="categories[]"
                    aria-label="multiple select example"
                    v-model="selectedCategories"
                  >
                    <option :value="item.id" v-for="item in categories" :key="item.id">
                      {{ item.name }}
                    </option>
                  </select>
                  <small class="text-danger" v-if="errors.categories != null"
                    >*{{ errors.categories[0] }}</small
                  >
                </div>
                <div class="form-group mb-3">
                  <label for="title" class="fw-semibold">Title</label>
                  <input
                    type="text"
                    class="form-control"
                    id="title"
                    name="title"
                    placeholder="Write post title!"
                    v-model="post.title"
                  />
                  <small class="text-danger" v-if="errors.title != null"
                    >*{{ errors.title[0] }}</small
                  >
                </div>
                <div class="form-group mb-3">
                  <label for="body" class="fw-semibold">Body</label>
                  <textarea
                    class="form-control"
                    id="body"
                    name="body"
                    rows="5"
                    placeholder="Write details here!"
                    v-model="post.body"
                  ></textarea>
                  <small class="text-danger" v-if="errors.body != null"
                    >*{{ errors.body[0] }}</small
                  >
                </div>
                <div class="text-center">
                  <button class="btn btn-secondary mr-3" type="reset">
                    <i class="fa-solid fa-trash-arrow-up"></i>Clear
                  </button>
                  <button class="btn btn-primary" type="submit">
                    <i class="fa-regular fa-floppy-disk"></i> Save
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Swal from "sweetalert2";

const Toast = Swal.mixin({
  toast: true,
  position: "top-right",
  customClass: {
    popup: "colored-toast",
  },
  showConfirmButton: false,
  timer: 1500,
  timerProgressBar: true,
});
export default {
  head: {
    title: "Post",
  },
  data() {
    return {
      url: null,
      categories: [],
      selectedCategories: [],
      errors: {},
      post: [],
    };
  },
  async fetch() {
    await this.getPost();
    await this.getAllCategories();
  },
  methods: {
    async getPost() {
      await this.$axios
        .$get(`http://127.0.0.1:8000/api/posts/${this.$route.params.id}`)
        .then((res) => {
          this.post = res;
          this.post.categories.map((item) => {
            this.selectedCategories.push(item.id);
          });
        })
        .catch((err) => {
          console.error(err);
        });
    },
    async getAllCategories() {
      await this.$axios
        .$get("http://127.0.0.1:8000/api/categories")
        .then((res) => {
          this.categories = res;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    imageChangeHandler(e) {
      let imgFrame = document.getElementById("img-frame");
      imgFrame.innerHTML = "";
      for (var i = 0; i < e.target.files.length; i++) {
        let file = e.target.files[i];
        if (file.type.includes("image")) {
          let img = document.createElement("img");
          img.classList.add("rounded"); //rounded img-fluid
          img.setAttribute("src", URL.createObjectURL(file));
          imgFrame.appendChild(img);
        }
      }
    },
    async editPost() {
      let form = new FormData(document.getElementById("form"));
      await this.$axios
        .$post(`http://127.0.0.1:8000/api/posts/${this.post.id}`, form)
        .then((res) => {
          Toast.fire({
            icon: "success",
            title: "Updated Successfully!",
          });
          this.$router.push({
            path: "/posts",
          });
        })
        .catch((err) => {
          console.log(err.response.data)
          if (err.response.status == 403) {
            this.$router.push({
              path: "/posts",
            });
            alert("You are not authorized!");
          } else {
            this.errors = err.response.data.errors;
          }
        });
    },
  },
};
</script>

<style></style>
