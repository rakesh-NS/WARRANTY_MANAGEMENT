import React, { useState, useEffect } from 'react';
import { Container, Row, Col, Card, Button, Spinner, Alert, Form, Badge } from 'react-bootstrap';
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
      setProducts(response.data || []);
    } catch (err) {
      setError('Failed to load your registered products');
    } finally {
      setLoading(false);
    }
  };

  const handleSearch = async (e) => {
    e.preventDefault();
    if (!searchQuery.trim()) {
      fetchProducts();
      return;
    }

    try {
      const response = await productService.search(searchQuery);
      setProducts(response.data || []);
    } catch (err) {
      setError('Search failed. Please try again.');
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm('Delete this product from your account?')) {
      return;
    }

    try {
      await productService.delete(id);
      setProducts((current) => current.filter((p) => p.id !== id));
    } catch (err) {
      setError('Delete failed. Please try again.');
    }
  };

  if (loading) return <Spinner animation="border" className="d-block mx-auto mt-5" />;

  return (
    <Container className="py-5">
      <div className="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
          <div className="text-primary fw-semibold">Warranty portal</div>
          <h1 className="mb-0">My products</h1>
        </div>
        <Link to="/products/add" className="btn btn-primary">
          + Register product
        </Link>
      </div>

      {error && <Alert variant="danger">{error}</Alert>}

      <Form onSubmit={handleSearch} className="mb-4">
        <div className="input-group">
          <Form.Control
            type="text"
            placeholder="Search by product name, brand or model"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
          />
          <Button variant="outline-secondary" type="submit">Search</Button>
        </div>
      </Form>

      {!products.length ? (
        <Alert variant="light" className="border">
          No products registered yet. Start by registering your first product to activate warranty coverage.
        </Alert>
      ) : (
        <Row className="g-4">
          {products.map((product) => (
            <Col lg={6} key={product.id}>
              <Card className="h-100 shadow-sm border-0">
                <Card.Body className="d-flex flex-column">
                  <div className="d-flex justify-content-between align-items-start mb-3">
                    <div>
                      <h5 className="mb-1">{product.productName}</h5>
                      <div className="text-muted">{product.brand} • {product.model}</div>
                    </div>
                    <Badge bg="success">Active warranty</Badge>
                  </div>

                  <div className="small text-muted mb-2">
                    <div><strong>Serial:</strong> {product.serialNumber}</div>
                    <div><strong>Category:</strong> {product.category || 'Not specified'}</div>
                    <div><strong>Purchased:</strong> {product.purchaseDate}</div>
                    <div><strong>Price:</strong> ${Number(product.price || 0).toFixed(2)}</div>
                  </div>

                  <div className="mt-auto pt-3 border-top">
                    <div className="d-flex gap-2 flex-wrap">
                      <Link to="/warranty" className="btn btn-outline-primary btn-sm">View warranty</Link>
                      <Link to={`/claims?productId=${product.id}`} className="btn btn-outline-success btn-sm">Raise claim</Link>
                      <Link to={`/products/edit/${product.id}`} className="btn btn-outline-warning btn-sm">Edit</Link>
                      <Button variant="outline-danger" size="sm" onClick={() => handleDelete(product.id)}>
                        Delete
                      </Button>
                    </div>
                  </div>
                </Card.Body>
              </Card>
            </Col>
          ))}
        </Row>
      )}
    </Container>
  );
};

export default ProductListPage;
