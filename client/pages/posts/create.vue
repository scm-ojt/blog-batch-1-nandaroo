<template>
  <div class="container">
    <div class="row d-flex justify-content-center">
      <div class="col-10">
        <div class="card shadow-lg d-block">
          <div class="card-header">
            <h4 class="fw-bolder text-center text-uppercase">Post Create</h4>
          </div>
          <div class="card-body">
            <div class="row h-auto">
              <div id="img-frame" class="mb-2">
                <div class="edit-img">
                  <img
                    src="../../assets/img/default-img.jpg"
                    class="rounded"
                    alt="user selected image"
                  />
                </div>
              </div>
            </div>
            <div class="row">
              <form @submit.prevent="createPost()" id="form">
                <div class="mb-3">
                  <label for="formFile" class="form-label fw-semibold"
                    >Choose Image</label
                  >
                  <input
                    class="form-control"
                    type="file"
                    name="image[]"
                    id="images"
                    multiple="multiple"
                    @change="imageChangeHandler"
                  />
                  <small class="text-danger" v-if="errors['image.0'] != null"
                    >*{{ errors["image.0"][0] }}</small
                  ><small class="text-danger" v-if="errors.image != null"
                    >*{{ errors.image[0] }}</small
                  >
                </div>

                <div class="form-group mb-3">
                  <label for="name" class="fw-semibold">Select Category</label>
                  <select
                    class="form-select"
                    multiple
                    name="categories[]"
                    aria-label="multiple select example"
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
      categories: [],
      errors: {},
    };
  },
  async fetch() {
    this.getAllCategories();
  },
  methods: {
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
        let div = document.createElement("div");
        let span = document.createElement("span");
        span.className = "position-absolute top-0 start-100 translate-middle mt-1";
        span.innerHTML = `<i
                      class="fa-solid fa-circle-xmark text-secondary bg-white rounded-circle fs-5"
                    ></i>`;
        span.onclick = (event) => this.removeImage(event, file);

        div.appendChild(span);
        div.classList.add("edit-img", "position-relative", "d-inline-block");
        let img = document.createElement("img");
        img.classList.add("rounded");
        if (file.type.includes("image")) {
          img.setAttribute("src", URL.createObjectURL(file));
        } else {
          img.setAttribute("src", "/_nuxt/assets/img/default-img.jpg");
          img.className = "rounded border border-danger";
        }
        div.appendChild(img);
        imgFrame.appendChild(div);
      }
    },
    async createPost() {
      let form = new FormData(document.getElementById("form"));
      await this.$axios
        .$post("http://127.0.0.1:8000/api/posts", form, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((res) => {
          Toast.fire({
            icon: "success",
            title: "Created Successfully!",
          });
          this.$router.push({
            path: "/posts",
          });
        })
        .catch((err) => {
          this.errors = err.response.data.errors;
        });
    },
    removeImage(event, image) {
      event.currentTarget.parentElement.remove();

      let imgFrame = document.getElementById("images");

      let files = Array.from(imgFrame.files);

      files = files.filter((file) => {
        return file != image;
      });

      let fileBuffer = new DataTransfer();
      for (let i = 0; i < files.length; i++) {
        fileBuffer.items.add(files[i]);
      }
      imgFrame.files = fileBuffer.files;
    },
  },
};
</script>

<style>
.edit-img {
  height: 150px;
  width: 200px;
  margin: 10px;
}
.edit-img img,
.hello {
  width: 100%;
  height: 100%;
}
.form-select option {
  padding: 3px 0;
}
</style>
