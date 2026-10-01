import React, { useState, useEffect } from 'react';
import { Container, Row, Col, Card, Spinner, Alert } from 'react-bootstrap';
import { adminService, productService } from '../services/serviceApi';

const DashboardPage = () => {
  const user = JSON.parse(localStorage.getItem('user') || 'null');
  const [stats, setStats] = useState(null);
  const [recentProducts, setRecentProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    if (user?.role === 'ADMIN') {
      fetchDashboardStats();
    } else {
      fetchUserOverview();
    }
  }, [user]);

  const fetchDashboardStats = async () => {
    try {
      const response = await adminService.getDashboard();
      setStats(response.data);
    } catch (err) {
      setError('Failed to load dashboard');
    } finally {
      setLoading(false);
    }
  };

  const fetchUserOverview = async () => {
    try {
      const response = await productService.getAll();
      const products = response.data || [];
      setRecentProducts(products.slice(0, 5));
      setStats({
        totalProducts: products.length,
        activeWarranties: products.length > 0 ? 1 : 0,
        pendingClaims: 0,
      });
    } catch (err) {
      setError('Failed to load your dashboard');
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <Spinner animation="border" />;

  return (
    <Container className="py-5">
      <h1 className="mb-4">{user?.role === 'ADMIN' ? 'Admin Dashboard' : `Customer Dashboard`}</h1>
      {error && <Alert variant="danger">{error}</Alert>}

      {user?.role === 'ADMIN' && stats && (
        <Row className="g-4">
          <Col md={4}>
            <Card className="shadow-sm text-center">
              <Card.Body>
                <h5>Total Users</h5>
                <h2 className="text-primary">{stats.totalUsers}</h2>
              </Card.Body>
            </Card>
          </Col>
          <Col md={4}>
            <Card className="shadow-sm text-center">
              <Card.Body>
                <h5>Total Products</h5>
                <h2 className="text-success">{stats.totalProducts}</h2>
              </Card.Body>
            </Card>
          </Col>
          <Col md={4}>
            <Card className="shadow-sm text-center">
              <Card.Body>
                <h5>Active Warranties</h5>
                <h2 className="text-info">{stats.activeWarranties}</h2>
              </Card.Body>
            </Card>
          </Col>
          <Col md={4}>
            <Card className="shadow-sm text-center">
              <Card.Body>
                <h5>Expired Warranties</h5>
                <h2 className="text-warning">{stats.expiredWarranties}</h2>
              </Card.Body>
            </Card>
          </Col>
          <Col md={4}>
            <Card className="shadow-sm text-center">
              <Card.Body>
                <h5>Pending Claims</h5>
                <h2 className="text-danger">{stats.pendingClaims}</h2>
              </Card.Body>
            </Card>
          </Col>
          <Col md={4}>
            <Card className="shadow-sm text-center">
              <Card.Body>
                <h5>Approved Claims</h5>
                <h2 className="text-success">{stats.approvedClaims}</h2>
              </Card.Body>
            </Card>
          </Col>
        </Row>
      )}

      {user?.role !== 'ADMIN' && stats && (
        <>
          <Row className="g-4 mb-4">
            <Col md={4}>
              <Card className="shadow-sm text-center">
                <Card.Body>
                  <h5>Registered Products</h5>
                  <h2 className="text-primary">{stats.totalProducts}</h2>
                </Card.Body>
              </Card>
            </Col>
            <Col md={4}>
              <Card className="shadow-sm text-center">
                <Card.Body>
                  <h5>Active Warranties</h5>
                  <h2 className="text-success">{stats.activeWarranties}</h2>
                </Card.Body>
              </Card>
            </Col>
            <Col md={4}>
              <Card className="shadow-sm text-center">
                <Card.Body>
                  <h5>Pending Claims</h5>
                  <h2 className="text-warning">{stats.pendingClaims}</h2>
                </Card.Body>
              </Card>
            </Col>
          </Row>

          <Card className="shadow-sm">
            <Card.Body>
              <h4 className="mb-3">Recent Products</h4>
              {recentProducts.length > 0 ? (
                <ul className="list-group list-group-flush">
                  {recentProducts.map((product) => (
                    <li key={product.id} className="list-group-item d-flex justify-content-between align-items-center">
                      <div>
                        <strong>{product.productName}</strong>
                        <div className="text-muted small">{product.brand} • {product.model}</div>
                      </div>
                      <span className="badge bg-primary rounded-pill">${product.price}</span>
                    </li>
                  ))}
                </ul>
              ) : (
                <p className="text-muted mb-0">No products registered yet. Add your first product to get started.</p>
              )}
            </Card.Body>
          </Card>
        </>
      )}
    </Container>
  );
};

export default DashboardPage;
