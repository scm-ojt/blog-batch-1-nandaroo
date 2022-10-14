<template>
  <div class="container">
    <div class="row d-flex justify-content-start">
      <div class="col-9">
        <NuxtLink to="/posts/create">
          <button class="btn btn-sm btn-primary">
            <i class="fa-sharp fa-solid fa-plus"></i> Create
          </button>
        </NuxtLink>
        <button class="btn btn-sm btn-info ml-1" @click="exportExcel()">
          <i class="fa-solid fa-file-arrow-down"></i> Export
        </button>
        <button
          type="button"
          class="btn btn-secondary btn-sm ml-1"
          data-bs-toggle="modal"
          data-bs-target="#importModal"
          data-bs-whatever="@getbootstrap"
        >
          <i class="fa-solid fa-file-arrow-up"></i> Import
        </button>
        <div
          class="modal fade"
          id="importModal"
          tabindex="-1"
          aria-labelledby="importModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Posts</h5>
                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Close"
                  id="modal-close"
                  @click="clearErrMsg()"
                ></button>
              </div>
              <div class="modal-body">
                <div
                  class="alert alert-sm alert-warning alert-dismissible fade show"
                  role="alert"
                  v-if="fileErr"
                >
                  Something went wrong!
                  <!-- <button
                    type="button"
                    class="btn-close btn-sm"
                    data-bs-dismiss="alert"
                    
                  ></button> -->
                </div>
                <form id="import-form">
                  <div class="mb-3">
                    <label for="file" class="col-form-label">Choose excel file:</label>
                    <input class="form-control" type="file" name="file" id="file" />
                    <small class="text-danger" v-if="fileErrMsg != ''"
                      >*{{ fileErrMsg }}</small
                    >
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button
                  type="button"
                  class="btn btn-sm btn-primary"
                  @click="importExcel()"
                >
                  Submit
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-3 d-flex justify-content-start">
        <div class="input-group input-group-sm mb-3">
          <input
            type="text"
            class="form-control"
            v-model="keyword"
            placeholder="Search"
          />
          <button class="input-group-text bg-primary text-white" @click="search()">
            <i class="fa-sharp fa-solid fa-magnifying-glass"></i> Search
          </button>
        </div>
      </div>
    </div>
    <div class="row">
      <b-table
        striped
        id="my-table"
        :items="posts"
        :per-page="perPage"
        :current-page="currentPage"
        :fields="fields"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        class="mb-5"
      >
        <template #cell(image)="data">
          <img
            :src="`http://localhost:8000/storage/img/posts/${data.item.image}`"
            class="rounded img-fluid post-img"
            alt="data.item.id"
          />
        </template>
        <template #cell(user)="data">
          {{ data.item.user.name }}
        </template>
        <template #cell(categories)="data">
          <span v-for="(category, index) in data.item.categories" :key="category.id">
            <span v-if="index != 0" class="pr-2">,</span>
            {{ category.name }}</span
          >
        </template>
        <template #cell(actions)="data">
          <NuxtLink
            :to="`/posts/edit/${data.item.id}`"
            v-if="$auth.user.id == data.item.user_id"
          >
            <button class="btn btn-sm btn-success my-2">
              <i class="fa-regular fa-pen-to-square"></i> Edit
            </button>
          </NuxtLink>
          <button
            class="btn btn-sm btn-danger my-2"
            v-if="$auth.user.id == data.item.user.id"
            @click="deletePost(data.item.id)"
          >
            <i class="fa-solid fa-trash-can"></i> Delete
          </button>
          <NuxtLink :to="`/posts/${data.item.id}`">
            <button class="btn btn-sm btn-secondary my2">
              <i class="fa-sharp fa-solid fa-circle-info"></i> Details
            </button>
          </NuxtLink>
        </template>
      </b-table>
      <p v-if="rows == 0" class="text-danger text-center">No post here!</p>
      <b-pagination
        v-model="currentPage"
        :total-rows="rows"
        :per-page="perPage"
        :current-page="currentPage"
        first-text="First"
        prev-text="Prev"
        next-text="Next"
        last-text="Last"
        v-if="rows > 5"
      ></b-pagination>
    </div>
  </div>
</template>

<script>
export default {
  head: {
    title: "Post",
  },
  data() {
    return {
      posts: [],
      keyword: "",
      fields: [
        { key: "image", label: "Image", thStyle: { width: "20%" } },
        "User",
        { key: "categories", label: "Categories", thStyle: { width: "15%" } },
        {
          key: "title",
          label: "Title",
          thStyle: { width: "30%" },
          tdClass: "semibolder",
        },
        { key: "actions", label: "Actions", thStyle: { width: "20%" } },
      ],
      sortBy: "id",
      sortDesc: true,
      currentPage: 1,
      perPage: 5,
      file: null,
      fileErr: false,
      fileErrMsg: "",
    };
  },
  mounted() {
    this.getAllPosts();
  },
  methods: {
    async getAllPosts() {
      await this.$axios
        .$get("http://127.0.0.1:8000/api/posts?search=" + this.keyword)
        .then((res) => {
          this.posts = res;
        })
        .catch((err) => {
          console.error(err);
        });
    },
    deletePost(id) {
      if (confirm("Are you sure to delete?")) {
        this.$axios
          .$delete(`posts/${id}`)
          .then((res) => {
            this.posts = this.posts.filter((item) => {
              return item.id !== id;
            });
          })
          .catch((err) => {
            if (err.response.status == 403) {
              alert("You are not authorized!");
            }
          });
      }
    },
    search() {
      this.getAllPosts();
    },
    exportExcel() {
      this.$axios
        .$post(
          "http://127.0.0.1:8000/api/posts/export",
          { keyword: this.keyword },
          { responseType: "arraybuffer" }
        )
        .then((response) => {
          let fileURL = window.URL.createObjectURL(new Blob([response]));
          let fileLink = document.createElement("a");
          fileLink.href = fileURL;
          fileLink.setAttribute("download", "posts.xlsx");
          document.body.appendChild(fileLink);
          fileLink.click();
        })
        .catch((err) => {
          console.error(err);
        });
    },
    importExcel() {
      let form = document.getElementById("import-form");
      let formData = new FormData(form);
      this.$axios
        .$post("http://127.0.0.1:8000/api/posts/import", formData)
        .then((res) => {
          document.getElementById("modal-close").click();
          form.reset();
          this.getAllPosts();
          this.fileErr = false;
          this.fileErrMsg = "";
        })
        .catch((err) => {
          this.fileErr = true;
          if (err.response.status == 422) {
            this.fileErrMsg = err.response.data.errors.file[0];
          }
        });
    },
    clearErrMsg() {
      this.fileErrMsg = "";
      this.fileErr = false;
      let form = document.getElementById("import-form");
      form.reset();
    },
  },
  computed: {
    rows() {
      return this.posts.length;
    },
  },
};
</script>

<style>
.post {
  max-height: 200px;
}
.post-body {
  width: 200px;
  height: 150px;
  white-space: wrap;
  text-overflow: ellipsis;
}

.post-img {
  max-height: 150px;
  min-width: 200px;
}

.semibolder {
  font-weight: 600;
}
</style>
