# LAPORAN PROJEK AKHIR: INTEGRASI VUE JS & BACKEND API

Mata Kuliah: Pemrograman Web Lanjut

----------

## 1. Identitas Kelompok

-   KELOMPOK: [Isi Nomor Kelompok]
    
-   Nama Ketua / NIM: [Isi Nama & NIM]
    
-   Anggota 1 / NIM: [Isi Nama & NIM]
    
-   Anggota 2 / NIM: [Isi Nama & NIM]
    

-   ----------
    

## 2. Tautan (Links)

-   Link Repository GitHub (Front-end): [https://github.com/](https://github.com/)
    
-   Link Repository GitHub (Back-end): [https://github.com/](https://github.com/)
    
-   Link Video Demo: [https://www.youtube.com/channel/UC5rBpVgv83gYPZ593XwQUsA](https://www.youtube.com/channel/UC5rBpVgv83gYPZ593XwQUsA)
    
-   Video menunjukan Demo Fitur Per anggota
    

----------

## 3. Implementasi Fitur Kelompok

Bagian ini mencakup layanan autentikasi berbasis token dan manajemen file yang digunakan oleh seluruh anggota tim.

### a. Autentikasi (Login & Register)

Integrasi menggunakan token-based authentication yang disimpan pada localStorage.

-   Endpoint:  POST /api/login
    
-   Implementasi Fetch dan UI:
    

Kode Komponen (Login.vue)

<template>

<div class="login-container">

<h3>Login Sistem</h3>

<form @submit.prevent="handleLogin">

<input v-model="email" type="email" placeholder="Email" required />

<input v-model="password" type="password" placeholder="Password" required />

<button type="submit">Masuk</button>

</form>

</div>

</template>

  

<script>

export default {

data() {

return {

email: '',

password: ''

};

},

methods: {

async handleLogin() {

try {

const response = await fetch('http://localhost:8000/api/login', {

method: 'POST',

headers: { 'Content-Type': 'application/json' },

body: JSON.stringify({

email: this.email,

password: this.password

})

});

  

const result = await response.json();

  

if (result.success) {

// Menyimpan token dan role ke LocalStorage

localStorage.setItem('token', result.data.token);

localStorage.setItem('role', result.data.role);

alert("Login Berhasil!");

this.$router.push('/dashboard'); // Navigasi ke halaman utama

} else {

alert("Gagal: " + result.message);

}

} catch (error) {

console.error("Error saat login:", error);

}

}

}

};

</script>

-   Penjelasan Kode: [Semakin detail alur penjelasanya semakin baik]
    
-   Screenshot UI: [Lampirkan screenshot halaman]
    

### b. Upload Gambar (Global Service)

Layanan untuk mengirim file gambar menggunakan FormData.

-   Endpoint:  POST /api/products (atau endpoint upload lainnya)
    
-   Implementasi Fetch dan UI:
    

  

Kode Komponen (UploadProfile.vue)

<template>

<div class="upload-section">

<input type="file" @change="onFileChange" />

<button @click="submitUpload">Unggah Foto</button>

</div>

</template>

  

<script>

export default {

data() {

return {

selectedFile: null

};

},

methods: {

onFileChange(event) {

// Menangkap file dari event change input

this.selectedFile = event.target.files[0];

},

async submitUpload() {

if (!this.selectedFile) return alert("Pilih file terlebih dahulu");

  

const formData = new FormData();

formData.append('image', this.selectedFile);

formData.append('title', 'Update Gambar User');

  

const response = await fetch('http://localhost:8000/api/products', {

method: 'POST',

headers: {

// Authorization token diambil dari storage

'Authorization': `Bearer ${localStorage.getItem('token')}`

},

body: formData // Body berisi objek FormData

});

  

const result = await response.json();

if (result.success) {

alert("Gambar Berhasil Diunggah!");

}

}

}

};

</script>

-   Penjelasan Kode: [Semakin detail alur penjelasanya semakin baik]
    
-   Screenshot UI: [Lampirkan screenshot halaman
    

----------

4. Implementasi Fitur Individu

### Anggota 1: [Nama Anggota 1] - Layanan Tambah & Lihat Data (C, R)

Tanggung Jawab: Mengelola input data produk baru ke tabel.

-   Endpoint:  POST /api/products dan GET /api/products
    
-   Fitur: Menambahkan data "Mac Book Pro" ke database melalui form Vue.
    
-   Snippet Kode (Insert Data):
    

Kode Komponen (ProductManager.vue)

<template>

<div>

<section>

<h3>Tambah Produk Baru</h3>

<input v-model="newProduct.title" placeholder="Nama Produk" />

<input v-model="newProduct.price" type="number" placeholder="Harga" />

<button @click="saveProduct">Simpan</button>

</section>

  

<section>

<h3>Daftar Produk</h3>

<table border="1">

<tr v-for="item in products" :key="item.id">

<td>{{ item.title }}</td>

<td>{{ item.price }}</td>

</tr>

</table>

</section>

</div>

</template>

  

<script>

export default {

data() {

return {

products: [],

newProduct: { title: '', price: '' }

};

},

methods: {

// FUNGSI READ

async getProducts() {

const response = await fetch('http://localhost:8000/api/products', {

headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }

});

const result = await response.json();

this.products = result.data;

},

// FUNGSI INSERT

async saveProduct() {

await fetch('http://localhost:8000/api/products', {

method: 'POST',

headers: {

'Content-Type': 'application/json',

'Authorization': `Bearer ${localStorage.getItem('token')}`

},

body: JSON.stringify(this.newProduct)

});

this.getProducts(); // Refresh tabel setelah simpan

}

},

mounted() {

// Memanggil data saat komponen dimuat

this.getProducts();

}

};

</script>

-   Penjelasan Kode:[Semakin detail alur penjelasanya semakin baik]
    
-   Screenshot UI: [Lampirkan screenshot form input dan tabel data]
    

----------

### Anggota 2: [Nama Anggota 2] - Layanan Detail & Hapus (R, D)

Tanggung Jawab: Mengelola tampilan detail data dan penghapusan record.

-   Endpoint:  GET /api/products/{id} dan DELETE /api/products/{id}
    
-   Fitur: Mengambil detail data menggunakan ProductResource.
    
-   Snippet Kode (Show Detail):
    

Kode Komponen Vue (ProductDetail.vue)

<template>

<div class="container">

<h2>Detail Produk</h2>

<div v-if="!product">Memuat data...</div>

  

<div v-else class="card">

<img :src="product.image_url" alt="Gambar Produk" width="300" />

<h1>{{ product.title }}</h1>

<p><strong>Harga:</strong> Rp{{ product.price }}</p>

<p><strong>Deskripsi:</strong> {{ product.description }}</p>

<p><strong>Stok:</strong> {{ product.stock }}</p>

  

<hr />

<div v-if="userRole === 'admin'" class="admin-actions">

<button @click="confirmDelete" class="btn-delete">Hapus Produk Ini</button>

</div>

<button @click="$router.push('/products')">Kembali ke Daftar</button>

</div>

</div>

</template>

  

<script>

export default {

data() {

return {

product: null, // Menampung data objek produk

userRole: '', // Menyimpan role user (admin/member)

token: '' // Menyimpan token untuk auth

};

},

methods: {

// 1. FUNGSI UNTUK MENGAMBIL DETAIL DATA (READ DETAIL)

async getProductDetail() {

const productId = this.$route.params.id; // Mengambil ID dari URL

try {

const response = await fetch(`http://localhost:8000/api/products/${productId}`, {

method: 'GET',

headers: {

'Authorization': `Bearer ${this.token}`,

'Accept': 'application/json'

}

});

const result = await response.json();

if (result.success) {

this.product = result.data; // Memasukkan data ke variabel product

}

} catch (error) {

console.error("Gagal mengambil detail:", error);

}

},

  

// 2. FUNGSI UNTUK MENGHAPUS DATA (DELETE)

async confirmDelete() {

if (confirm("Apakah Anda yakin ingin menghapus produk ini?")) {

try {

const response = await fetch(`http://localhost:8000/api/products/${this.product.id}`, {

method: 'DELETE',

headers: {

'Authorization': `Bearer ${this.token}`

}

});

const result = await response.json();

if (result.success) {

alert("Produk Berhasil Dihapus!");

this.$router.push('/products'); // Kembali ke daftar produk

}

} catch (error) {

alert("Gagal menghapus data");

}

}

}

},

mounted() {

// Inisialisasi data saat halaman dibuka

this.token = localStorage.getItem('token');

this.userRole = localStorage.getItem('role');

this.getProductDetail();

}

};

</script>

-   Penjelasan Kode: [Semakin detail alur penjelasanya semakin baik]
    
-   Screenshot UI: [Lampirkan screenshot halaman detail data]
    

----------

## 5. Pemisahan Role (Admin & Member)

Jelaskan logika pembatasan hak akses pada aplikasi Front-end:

1.  Admin: Memiliki akses ke tombol "Tambah", "Edit", dan "Hapus".
    
2.  Member: Hanya dapat melihat daftar data (Read-only).
    

-   Penerapan di Vue:
    

<button v-if="role === 'admin'" @click="deleteItem(id)">Hapus Data</button>

  

----------

### Lampiran: Screenshot Hasil Akhir

1.  Screenshot Halaman per Fitur Vue.js
    
2.  Screenshot Halaman Dashboard Vue.js.
    
3.  Screenshot Notifikasi Gagal (Jika Password/Input salah).
