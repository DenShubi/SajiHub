# Kelompok 2
Ridho Denindra Shobirin Cholil Soenarno / 235150407111066
M. Fachri Abdurafi / 235150400111025

---

## Ridho Denindra Shobirin Cholil Soenarno / 235150407111066

### 1. Get All Menu
**Nama Endpoint:** Get All Menu
**Tujuan Endpoint:** Endpoint ini mengambil seluruh data menu dari tabel `menus` yang bersifat publik sehingga bisa dilihat oleh siapapun.
**Method:** GET
**URL:** `http://localhost:8000/api/menus`
**Headers (Jika Ada):** *Tidak ada (Public)*
**Body Request:** 
```json
{}
```
**Response Sukses:** 
```json
[
  {
    "id": 1,
    "name": "Nasi Goreng Spesial",
    "description": "Nasi goreng dengan bumbu rahasia dan telur mata sapi.",
    "price": 25000,
    "image_url": "/storage/menus/K3T18woVasaBgyGwT33sX627wN7kMeTo4e3I13Ay.jpg",
    "created_at": "2026-04-30T11:37:54.000000Z",
    "updated_at": "2026-04-30T11:37:54.000000Z"
  }
]
```
**Response Gagal:** *(Tidak ada response gagal khusus, selain 500 Server Error jika sistem bermasalah)*
**Screenshot hasil:**
*(Sisipkan Screenshot Postman Kamu Disini)*

**Class Controller:** `MenuController`
**Fungsi Pada Class Controller:** `index()`
**Code Fungsi:**
```php
public function index()
{
    $menus = Menu::all();
    return response()->json($menus);
}
```

### 2. Create Menu
**Nama Endpoint:** Create Menu
**Tujuan Endpoint:** Endpoint ini menambahkan data menu baru beserta upload file gambar ke dalam database. Hanya pengguna dengan role Admin yang memiliki akses.
**Method:** POST
**URL:** `http://localhost:8000/api/menus`
**Headers (Jika Ada):** 
- `Authorization`: Bearer TOKEN_KAMU
- `Accept`: application/json
**Body Request (form-data):** 
```json
{
  "name": "Nasi Goreng Spesial",
  "description": "Nasi goreng dengan bumbu rahasia dan telur mata sapi.",
  "price": 25000,
  "image": "image_file.jpg"
}
```
**Response Sukses:** 
```json
{
  "message": "Menu created successfully",
  "menu": {
    "name": "Nasi Goreng Spesial",
    "description": "Nasi goreng dengan bumbu rahasia dan telur mata sapi.",
    "price": "25000",
    "image_url": "/storage/menus/K3T18woVasaBgyGwT33sX627wN7kMeTo4e3I13Ay.jpg",
    "updated_at": "2026-04-30T11:37:54.000000Z",
    "created_at": "2026-04-30T11:37:54.000000Z",
    "id": 1
  }
}
```
**Response Gagal (Jika bukan Admin):** 
```json
{
  "message": "Forbidden. Admin access required."
}
```
**Screenshot hasil:**
*(Sisipkan Screenshot Postman Kamu Disini)*

**Class Controller:** `MenuController`
**Fungsi Pada Class Controller:** `store(Request $request)`
**Code Fungsi:**
```php
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $imageUrl = null;
    if ($request->hasFile('image')) {
        $imageUrl = $this->uploadImage($request, 'image', 'menus');
    }

    $menu = Menu::create([
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'image_url' => $imageUrl,
    ]);

    return response()->json([
        'message' => 'Menu created successfully',
        'menu' => $menu
    ], 201);
}
```

### 3. Update Menu
**Nama Endpoint:** Update Menu
**Tujuan Endpoint:** Endpoint ini mengubah data menu dan/atau gambar menu yang sudah ada di database. Hanya Admin yang dapat mengaksesnya.
**Method:** POST (menggunakan spoofing `_method=PUT`) atau PUT
**URL:** `http://localhost:8000/api/menus/{id}`
**Headers (Jika Ada):** 
- `Authorization`: Bearer TOKEN_KAMU
- `Accept`: application/json
**Body Request (form-data):** 
```json
{
  "_method": "PUT",
  "price": 30000
}
```
**Response Sukses:** 
```json
{
  "message": "Menu updated successfully",
  "menu": {
    "id": 1,
    "name": "Nasi Goreng Spesial",
    "description": "Nasi goreng dengan bumbu rahasia dan telur mata sapi.",
    "price": "30000",
    "image_url": "/storage/menus/K3T18woVasaBgyGwT33sX627wN7kMeTo4e3I13Ay.jpg",
    "created_at": "2026-04-30T11:37:54.000000Z",
    "updated_at": "2026-04-30T11:38:25.000000Z"
  }
}
```
**Response Gagal (Data tidak ditemukan):** 
```json
{
  "message": "Menu not found"
}
```
**Screenshot hasil:**
*(Sisipkan Screenshot Postman Kamu Disini)*

**Class Controller:** `MenuController`
**Fungsi Pada Class Controller:** `update(Request $request, $id)`
**Code Fungsi:**
```php
public function update(Request $request, $id)
{
    $menu = Menu::find($id);

    if (!$menu) {
        return response()->json(['message' => 'Menu not found'], 404);
    }

    $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'sometimes|required|numeric|min:0',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    if ($request->hasFile('image')) {
        // Delete old image if it exists
        if ($menu->image_url) {
            $oldPath = str_replace('/storage/', '', $menu->image_url);
            Storage::disk('public')->delete($oldPath);
        }
        $menu->image_url = $this->uploadImage($request, 'image', 'menus');
    }

    if ($request->has('name')) $menu->name = $request->name;
    if ($request->has('description')) $menu->description = $request->description;
    if ($request->has('price')) $menu->price = $request->price;

    $menu->save();

    return response()->json([
        'message' => 'Menu updated successfully',
        'menu' => $menu
    ]);
}
```

### 4. Delete Menu
**Nama Endpoint:** Delete Menu
**Tujuan Endpoint:** Endpoint ini menghapus data menu dari database beserta file gambar fisiknya di storage. Hanya Admin yang dapat mengaksesnya.
**Method:** DELETE
**URL:** `http://localhost:8000/api/menus/{id}`
**Headers (Jika Ada):** 
- `Authorization`: Bearer TOKEN_KAMU
- `Accept`: application/json
**Body Request:** 
```json
{}
```
**Response Sukses:** 
```json
{
  "message": "Menu deleted successfully"
}
```
**Response Gagal (Data tidak ditemukan):** 
```json
{
  "message": "Menu not found"
}
```
**Screenshot hasil:**
*(Sisipkan Screenshot Postman Kamu Disini)*

**Class Controller:** `MenuController`
**Fungsi Pada Class Controller:** `destroy($id)`
**Code Fungsi:**
```php
public function destroy($id)
{
    $menu = Menu::find($id);

    if (!$menu) {
        return response()->json(['message' => 'Menu not found'], 404);
    }

    // Delete associated image
    if ($menu->image_url) {
        $oldPath = str_replace('/storage/', '', $menu->image_url);
        Storage::disk('public')->delete($oldPath);
    }

    $menu->delete();

    return response()->json(['message' => 'Menu deleted successfully']);
}
```

---

## M. Fachri Abdurafi / 235150400111025

### 1. Register User
**Nama Endpoint:** Register
**Tujuan Endpoint:** Mendaftarkan pengguna baru dengan menyimpan datanya ke tabel `users` serta menetapkan rolenya (Admin atau Member), dan mengembalikan token otentikasi.
**Method:** POST
**URL:** `http://localhost:8000/api/register`
**Headers (Jika Ada):** 
- `Accept`: application/json
**Body Request:** 
```json
{
  "name": "Admin User",
  "email": "admin@sajihub.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "Admin"
}
```
**Response Sukses:** 
```json
{
  "message": "Registration successful",
  "access_token": "1|pc8AAUJVBoai4SKeWbb5rgXOlkH8ljKEvaTvzWU88281ba2d",
  "token_type": "Bearer",
  "user": {
    "name": "Admin User",
    "email": "admin@sajihub.com",
    "role": "Admin",
    "updated_at": "2026-04-30T11:37:08.000000Z",
    "created_at": "2026-04-30T11:37:08.000000Z",
    "id": 1
  }
}
```
**Response Gagal (Validasi Email Kembar):** 
```json
{
  "message": "The email has already been taken.",
  "errors": {
    "email": [
      "The email has already been taken."
    ]
  }
}
```
**Screenshot hasil:**
*(Sisipkan Screenshot Postman Kamu Disini)*

**Class Controller:** `AuthController`
**Fungsi Pada Class Controller:** `register(Request $request)`
**Code Fungsi:**
```php
public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'role' => 'nullable|in:Admin,Member'
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role ?? 'Member',
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Registration successful',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user
    ], 201);
}
```

### 2. Login User
**Nama Endpoint:** Login
**Tujuan Endpoint:** Memvalidasi kredensial login dan mengembalikan token otentikasi Sanctum (Bearer Token) agar pengguna bisa mengakses endpoint terbatas.
**Method:** POST
**URL:** `http://localhost:8000/api/login`
**Headers (Jika Ada):** 
- `Accept`: application/json
**Body Request:** 
```json
{
  "email": "admin@sajihub.com",
  "password": "password123"
}
```
**Response Sukses:** 
```json
{
  "message": "Login successful",
  "access_token": "3|AbCdEfGhIjKlMnOpQrStUvWxYz",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@sajihub.com",
    "role": "Admin",
    "email_verified_at": null,
    "created_at": "2026-04-30T11:37:08.000000Z",
    "updated_at": "2026-04-30T11:37:08.000000Z"
  }
}
```
**Response Gagal (Email/Password Salah):** 
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": [
      "The provided credentials are incorrect."
    ]
  }
}
```
**Screenshot hasil:**
*(Sisipkan Screenshot Postman Kamu Disini)*

**Class Controller:** `AuthController`
**Fungsi Pada Class Controller:** `login(Request $request)`
**Code Fungsi:**
```php
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $user->tokens()->delete();

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user
    ]);
}
```
