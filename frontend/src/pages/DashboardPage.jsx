import React, { useState, useEffect } from 'react';
import { Container, Row, Col, Card, Spinner } from 'react-bootstrap';
import { adminService } from '../services/serviceApi';

const DashboardPage = () => {
  const user = JSON.parse(localStorage.getItem('user'));
  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(user?.role === 'ADMIN');
  const [error, setError] = useState('');

  useEffect(() => {
    if (user?.role === 'ADMIN') {
      fetchDashboardStats();
    }
  }, []);

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

  if (loading) return <Spinner animation="border" />;

  return (
    <Container className="py-5">
      <h1 className="mb-4">Dashboard</h1>
      {error && <div className="alert alert-danger">{error}</div>}
      
      {stats && (
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
    </Container>
  );
};

export default DashboardPage;
