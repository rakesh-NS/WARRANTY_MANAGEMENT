import React, { useState, useEffect } from 'react';
import { Container, Row, Col, Card, Spinner } from 'react-bootstrap';
import { adminService } from '../services/serviceApi';

const AdminDashboardPage = () => {
  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchStats();
  }, []);

  const fetchStats = async () => {
    try {
      const response = await adminService.getDashboard();
      setStats(response.data);
    } catch (err) {
      console.error('Failed to load dashboard');
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <Spinner animation="border" />;

  return (
    <Container className="py-5">
      <h1 className="mb-4">Admin Dashboard</h1>
      {stats && (
        <Row className="g-4">
          <Col md={6}>
            <Card className="shadow-sm">
              <Card.Body className="text-center">
                <h5>Total Users</h5>
                <h2 className="text-primary">{stats.totalUsers}</h2>
              </Card.Body>
            </Card>
          </Col>
          <Col md={6}>
            <Card className="shadow-sm">
              <Card.Body className="text-center">
                <h5>Total Products</h5>
                <h2 className="text-success">{stats.totalProducts}</h2>
              </Card.Body>
            </Card>
          </Col>
          <Col md={4}>
            <Card className="shadow-sm">
              <Card.Body className="text-center">
                <h5>Active Warranties</h5>
                <h2 className="text-info">{stats.activeWarranties}</h2>
              </Card.Body>
            </Card>
          </Col>
          <Col md={4}>
            <Card className="shadow-sm">
              <Card.Body className="text-center">
                <h5>Expired Warranties</h5>
                <h2 className="text-warning">{stats.expiredWarranties}</h2>
              </Card.Body>
            </Card>
          </Col>
          <Col md={4}>
            <Card className="shadow-sm">
              <Card.Body className="text-center">
                <h5>Pending Claims</h5>
                <h2 className="text-danger">{stats.pendingClaims}</h2>
              </Card.Body>
            </Card>
          </Col>
          <Col md={4}>
            <Card className="shadow-sm">
              <Card.Body className="text-center">
                <h5>Approved Claims</h5>
                <h2 className="text-success">{stats.approvedClaims}</h2>
              </Card.Body>
            </Card>
          </Col>
          <Col md={4}>
            <Card className="shadow-sm">
              <Card.Body className="text-center">
                <h5>Rejected Claims</h5>
                <h2 className="text-danger">{stats.rejectedClaims}</h2>
              </Card.Body>
            </Card>
          </Col>
        </Row>
      )}
    </Container>
  );
};

export default AdminDashboardPage;
