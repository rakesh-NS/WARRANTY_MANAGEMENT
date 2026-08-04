import React, { useState, useEffect } from 'react';
import { Container, Table, Button, Spinner, Alert } from 'react-bootstrap';
import { Link } from 'react-router-dom';
import { productService } from '../services/serviceApi';

const ManageProductsPage = () => {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

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

  const handleDelete = async (id) => {
    if (window.confirm('Delete this product?')) {
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
      <h1 className="mb-4">Manage Products</h1>
      {error && <Alert variant="danger">{error}</Alert>}
      <Table striped bordered hover>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Brand</th>
            <th>Model</th>
            <th>User</th>
            <th>Price</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {products.map(p => (
            <tr key={p.id}>
              <td>{p.id}</td>
              <td>{p.productName}</td>
              <td>{p.brand}</td>
              <td>{p.model}</td>
              <td>{p.userName}</td>
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

export default ManageProductsPage;
