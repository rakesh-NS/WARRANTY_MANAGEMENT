import React, { useState, useEffect } from 'react';
import { Container, Table, Button, Spinner, Alert } from 'react-bootstrap';
import { Link } from 'react-router-dom';
import { productService } from '../services/serviceApi';

const ProductListPage = () => {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [searchQuery, setSearchQuery] = useState('');

  useEffect(() => {
    fetchProducts();
  }, []);

  const fetchProducts = async () => {
    try {
      const response = await productService.getAll();
      setProducts(response.data);
    } catch (err) {
      setError('Failed to load products');
    } finally {
      setLoading(false);
    }
  };

  const handleSearch = async (e) => {
    e.preventDefault();
    if (searchQuery.trim()) {
      try {
        const response = await productService.search(searchQuery);
        setProducts(response.data);
      } catch (err) {
        setError('Search failed');
      }
    } else {
      fetchProducts();
    }
  };

  const handleDelete = async (id) => {
    if (window.confirm('Are you sure?')) {
      try {
        await productService.delete(id);
        setProducts(products.filter(p => p.id !== id));
      } catch (err) {
        setError('Delete failed');
      }
    }
  };

  if (loading) return <Spinner animation="border" />;

  return (
    <Container className="py-5">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h1>My Products</h1>
        <Link to="/products/add" className="btn btn-primary">+ Add Product</Link>
      </div>

      {error && <Alert variant="danger">{error}</Alert>}

      <form onSubmit={handleSearch} className="mb-4">
        <div className="input-group">
          <input
            type="text"
            className="form-control"
            placeholder="Search products..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
          />
          <button className="btn btn-outline-secondary" type="submit">Search</button>
        </div>
      </form>

      <Table striped bordered hover>
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Serial Number</th>
            <th>Price</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {products.map(p => (
            <tr key={p.id}>
              <td>{p.productName}</td>
              <td>{p.brand}</td>
              <td>{p.model}</td>
              <td>{p.serialNumber}</td>
              <td>${p.price}</td>
              <td>
                <Link to={`/products/edit/${p.id}`} className="btn btn-sm btn-warning me-2">Edit</Link>
                <button onClick={() => handleDelete(p.id)} className="btn btn-sm btn-danger">Delete</button>
              </td>
            </tr>
          ))}
        </tbody>
      </Table>
    </Container>
  );
};

export default ProductListPage;
