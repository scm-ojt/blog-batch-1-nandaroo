<template>
  <div class="container">
    <div class="row d-flex justify-content-start">
      <div class="col-2">
        <NuxtLink to="/posts/create">
          <button class="btn btn-sm btn-primary">
            <i class="fa-sharp fa-solid fa-plus"></i> Create
          </button>
        </NuxtLink>
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
    title: 'Post'
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
          this.keyword = '';
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
