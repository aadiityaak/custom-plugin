# Product Categories Taxonomy

## Overview

Taxonomy `category_product` telah ditambahkan untuk mengkategorikan produk dalam plugin custom-plugin.

## Features

### 1. **Taxonomy Registration**

- **Name**: `category_product`
- **Post Type**: `custom_product`
- **Hierarchical**: Yes (seperti categories WordPress)
- **Public**: Yes
- **Show in Admin**: Yes
- **Show in Rest API**: Yes

### 2. **Admin Integration**

- **Admin Column**: Kategori ditampilkan di list products
- **Sortable**: Column kategori bisa di-sort
- **Quick Edit**: Support quick edit untuk kategori
- **Bulk Edit**: Support bulk edit kategori
- **Meta Box**: Kategori tersedia di edit product page

### 3. **Frontend Display**

- **Product Listing**: Kategori ditampilkan di shortcode products
- **Category Links**: Link ke archive kategori
- **Styling**: Clean minimal design dengan category tags

## Shortcodes

### 1. **Product Categories Shortcode**

```php
[product_categories]
```

#### Attributes:

- `show_count` - Tampilkan jumlah produk (yes/no, default: no)
- `hide_empty` - Sembunyikan kategori kosong (yes/no, default: yes)
- `orderby` - Urutkan berdasarkan (name/count/slug, default: name)
- `order` - Urutan (ASC/DESC, default: ASC)
- `limit` - Batasi jumlah kategori (0 = unlimited)
- `style` - Gaya tampilan (list/grid/dropdown, default: list)
- `class` - Custom CSS class

#### Examples:

```php
// Basic list
[product_categories]

// Grid dengan counter
[product_categories style="grid" show_count="yes"]

// Dropdown selector
[product_categories style="dropdown"]

// Limited categories
[product_categories limit="5" orderby="count" order="DESC"]
```

### 2. **Enhanced Product Display**

Shortcode `[custom_data type="products"]` sekarang menampilkan kategori:

```php
[custom_data type="products" limit="6"]
```

## Admin Usage

### Creating Categories:

1. Go to `Products > Categories` in admin
2. Add new category dengan nama dan description
3. Set parent category jika hierarchical
4. Save category

### Assigning to Products:

1. Edit produk di admin
2. Select kategori di meta box "Product Categories"
3. Save/Update produk

### Bulk Operations:

1. Go to `Products` list
2. Select multiple products
3. Choose "Edit" dari bulk actions
4. Assign categories di bulk edit screen

## Template Integration

### Single Product Template:

```php
// Display categories in single product
$categories = get_the_terms(get_the_ID(), 'category_product');
if ($categories && !is_wp_error($categories)) {
    foreach ($categories as $category) {
        echo '<span class="product-category">' . esc_html($category->name) . '</span>';
    }
}
```

### Archive Template:

```php
// Category archive page
if (is_tax('category_product')) {
    $term = get_queried_object();
    echo '<h1>Category: ' . esc_html($term->name) . '</h1>';
    if ($term->description) {
        echo '<p>' . esc_html($term->description) . '</p>';
    }
}
```

## Styling

### CSS Classes:

```css
/* Product listing categories */
.product-categories {
  margin: 0.5rem 0;
}

.category-tag {
  display: inline-block;
  background: #f0f0f0;
  padding: 0.25rem 0.5rem;
  margin: 0.125rem;
  border-radius: 3px;
  font-size: 0.875rem;
  color: #666;
  text-decoration: none;
}

/* Category shortcode styles */
.product-categories-list ul {
  list-style: none;
  padding: 0;
}

.product-categories-list li {
  margin: 0.5rem 0;
}

.product-categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.category-item {
  border: 1px solid #ddd;
  padding: 1rem;
  text-align: center;
}

.product-categories-dropdown select {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
}
```

## URL Structure

### Category Archives:

- Base: `/product-category/category-name/`
- Hierarchical: `/product-category/parent/child/`
- With products: Automatic WordPress archive

### Admin URLs:

- Category list: `/wp-admin/edit-tags.php?taxonomy=category_product&post_type=custom_product`
- Add category: `/wp-admin/edit-tags.php?taxonomy=category_product&post_type=custom_product`

## Database Structure

### Tables:

- `wp_terms` - Category names and slugs
- `wp_term_taxonomy` - Taxonomy data (category_product)
- `wp_term_relationships` - Product-category relationships
- `wp_termmeta` - Category meta data (if needed)

## Capabilities

### Required Permissions:

- `manage_categories` - Create/edit/delete categories
- `edit_posts` - Assign categories to products

### User Roles:

- **Administrator**: Full access
- **Editor**: Full access
- **Author**: Assign existing categories only
- **Contributor**: Assign existing categories only

---

**Status**: ✅ IMPLEMENTED  
**Version**: 1.0.0  
**Compatibility**: WordPress 5.0+
