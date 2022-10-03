<template>
  <div class="container my-5">
    <div class="row d-flex justify-content-end">
      <div class="col-3">
        <button class="btn btn-sm btn-primary" @click="createForm()">Create</button>
      </div>
      <div class="col-5 d-flex justify-content-end">
        <div class="input-group input-group-sm mb-3 w-50">
          <input type="text" class="form-control" v-model="keyword" />
          <button class="input-group-text btn btn-primary btn-sm" @click="search()">
            Search
          </button>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-4 px-3">
        <div class="card">
          <div class="card-header">
            <h5>{{ !isEditMode ? "Create" : "Edit" }}</h5>
          </div>
          <div class="card-body">
            <form @submit.prevent="isEditMode ? editCategory() : createNew()">
              <div class="form-group my-2">
                <label for="name">Name</label>
                <input
                  type="text"
                  class="form-control mt-2"
                  id="name"
                  placeholder="Enter Category Name!"
                  v-model="category.name"
                />
                <small class="text-danger" v-if="nameErr != ''">*{{ nameErr }}</small>
              </div>
              <button class="btn btn-sm btn-primary" type="submit">Save</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-8">
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col" class="w-75">Name</th>
              <th scope="col">Acions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in categories" :key="item.id">
              <td scope="row">{{ item.id }}</td>
              <td>{{ item.name }}</td>
              <td>
                <button class="btn btn-sm btn-success" @click="editForm(item)">
                  Edit
                </button>
                <button class="btn btn-sm btn-danger" @click="destory(item.id)">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  head: {
    title: "Category",
  },
  data() {
    return {
      category: {
        id: null,
        name: "",
      },
      categories: {},
      nameErr: "",
      isEditMode: false,
      keyword: "",
    };
  },
  async fetch() {
    this.nameErr = "";
    this.getAllCategories();
  },
  methods: {
    async getAllCategories(page= 1) {
      await this.$axios
        .$get("categories?page=" + page)
        .then((res) => {
          this.categories = res.data;
          console.log(this.categories);
        })
        .catch((err) => {
          console.error(err);
        });
    },
    async createNew() {
      await this.$axios
        .$post("categories", { name: this.category.name })
        .then((res) => {
          this.categories.push(res.data);
          this.category.name = "";
        })
        .catch((err) => {
          this.nameErr = err.response.data.errors.name[0];
        });
    },
    createForm() {
      this.isEditMode = false;
      this.category = {};
    },
    editForm(item) {
      this.isEditMode = true;
      this.nameErr = "";
      this.category = { ...item };
    },
    editCategory() {
      this.$axios
        .$put(`categories/${this.category.id}`, { name: this.category.name })
        .then((res) => {
          this.isEditMode = false;
          this.getAllCategories();
          this.category = {};
        })
        .catch((err) => {
          this.nameErr = err.response.data.errors.name[0];
        });
    },
    destory(id) {
      this.$axios
        .$delete(`categories/${id}`)
        .then((res) => {
          this.categories = this.categories.filter((item) => {
            return item.id !== id;
          });
        })
        .catch((err) => {
          console.log(err);
        });
    },
    search() {
      this.categories = this.categories.filter((item) => {
        return item.name.includes(this.search);
      });
    },
  },
};
</script>

<style></style>
