<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Table, TableHead, TableRow, TableCell, TableBody, TableHeader } from '@/components/ui/table';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';

const products = ref([]);
const error = ref('');
const name = ref('');
const description = ref('');
const price = ref('');
const image = ref<File|null>(null);
const selectedProduct = ref<any>(null);
const editingProduct = ref<any>(null);
const showDialog = ref(false);
const showSheet = ref(false);
const deleteConfirm = ref(false);
const productToDelete = ref<any>(null);
const user = ref(null);

const fetchUser = async () => {
  const token = localStorage.getItem('access_token');
  if (token) {
    try {
      const res = await fetch('/api/me', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json',
        },
      });
      if (res.ok) {
        user.value = await res.json();
      }
    } catch (e) {
      // Optionally handle error
    }
  }
};

const fetchProducts = async () => {
  error.value = '';
  const token = localStorage.getItem('access_token');
  try {
    const res = await fetch('/api/products', {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    products.value = await res.json();
  } catch (e) {
    error.value = 'Failed to load products';
  }
};

const resetForm = () => {
  name.value = '';
  description.value = '';
  price.value = '';
  image.value = null;
  editingProduct.value = null;
};

const openAddDialog = () => {
  resetForm();
  editingProduct.value = null;
  showDialog.value = true;
};

const openEditDialog = (product: any) => {
  editingProduct.value = product;
  name.value = product.name;
  description.value = product.description;
  price.value = product.price;
  image.value = null;
  showDialog.value = true;
};

const createOrUpdateProduct = async () => {
  error.value = '';
  const token = localStorage.getItem('access_token');
  const formData = new FormData();
  formData.append('name', name.value);
  formData.append('description', description.value);
  formData.append('price', price.value);
  if (image.value) formData.append('image', image.value);

  try {
    let res;
    if (editingProduct.value) {
      res = await fetch(`/api/products/${editingProduct.value.id}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}`, 'X-HTTP-Method-Override': 'PUT' },
        body: formData
      });
    } else {
      res = await fetch('/api/products', {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${token}` },
        body: formData
      });
    }
    if (res.ok) {
      await fetchProducts();
      showDialog.value = false;
      resetForm();
    } else {
      const data = await res.json();
      error.value = data.message || 'Failed to save product';
    }
  } catch (e) {
    error.value = 'Failed to save product';
  }
};

const confirmDeleteProduct = (product: any) => {
  productToDelete.value = product;
  deleteConfirm.value = true;
};

const deleteProduct = async () => {
  if (!productToDelete.value) return;
  error.value = '';
  const token = localStorage.getItem('access_token');
  try {
    const res = await fetch(`/api/products/${productToDelete.value.id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}` }
    });
    if (res.ok) {
      await fetchProducts();
      if (selectedProduct.value && selectedProduct.value.id === productToDelete.value.id) {
        selectedProduct.value = null;
      }
      deleteConfirm.value = false;
      productToDelete.value = null;
    } else {
      error.value = 'Failed to delete product';
    }
  } catch (e) {
    error.value = 'Failed to delete product';
  }
};

const openDetails = (product: any) => {
  selectedProduct.value = product;
  showSheet.value = true;
};

onMounted(async () => {
  await fetchUser();
  await fetchProducts();
});
</script>

<template>
  <AppLayout :user="user">
    <div class="max-w-5xl ml-12 py-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Products</h1>
        <Button @click="openAddDialog" variant="default">Add Product</Button>
      </div>

      <div v-if="error" class="mb-4 text-red-500">{{ error }}</div>

      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>Name</TableHead>
            <TableHead>Description</TableHead>
            <TableHead>Price</TableHead>
            <TableHead>Image</TableHead>
            <TableHead class="text-right">Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="product in products" :key="product.id" class="hover:bg-muted cursor-pointer" @click="openDetails(product)">
            <TableCell>{{ product.name }}</TableCell>
            <TableCell class="truncate max-w-[200px]">{{ product.description }}</TableCell>
            <TableCell>${{ product.price }}</TableCell>
            <TableCell>
              <img v-if="product.image" :src="`/storage/${product.image}`" alt="Product Image" class="h-10 w-16 object-cover rounded" />
            </TableCell>
            <TableCell class="text-right space-x-2">
              <Button size="sm" variant="outline" @click.stop="openEditDialog(product)">Edit</Button>
              <Button size="sm" variant="destructive" @click.stop="confirmDeleteProduct(product)">Delete</Button>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>

      <!-- Add/Edit Dialog -->
      <Dialog v-model:open="showDialog">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{{ editingProduct ? 'Edit Product' : 'Add Product' }}</DialogTitle>
          </DialogHeader>
          <form @submit.prevent="createOrUpdateProduct" class="space-y-4">
            <Input v-model="name" placeholder="Name" required />
            <Input v-model="description" placeholder="Description" />
            <Input v-model="price" type="number" step="0.01" placeholder="Price" required />
            <Input type="file" @change="e => image.value = e.target.files[0]" />
            <DialogFooter>
              <Button type="submit" variant="default">{{ editingProduct ? 'Update' : 'Add' }}</Button>
              <Button type="button" variant="outline" @click="showDialog = false">Cancel</Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>

      <!-- Delete Confirmation Dialog -->
      <Dialog v-model:open="deleteConfirm">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Product</DialogTitle>
          </DialogHeader>
          <p>Are you sure you want to delete <strong>{{ productToDelete?.name }}</strong>?</p>
          <DialogFooter>
            <Button variant="destructive" @click="deleteProduct">Delete</Button>
            <Button variant="outline" @click="deleteConfirm = false">Cancel</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <!-- Product Details Sheet -->
      <Sheet v-model:open="showSheet" side="right">
        <SheetContent class="w-[350px]">
          <SheetHeader>
            <SheetTitle>Product Details</SheetTitle>
          </SheetHeader>
          <div v-if="selectedProduct" class="space-y-2 mt-4">
            <div class="font-bold text-lg">{{ selectedProduct.name }}</div>
            <div class="text-muted-foreground">{{ selectedProduct.description }}</div>
            <div class="font-semibold">Price: ${{ selectedProduct.price }}</div>
            <div v-if="selectedProduct.image" class="mt-2">
              <img :src="`/storage/${selectedProduct.image}`" alt="Product Image" class="rounded w-full object-cover max-h-48" />
            </div>
          </div>
        </SheetContent>
      </Sheet>
    </div>
  </AppLayout>
</template>