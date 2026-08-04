import React from 'react';
import { Container, Row, Col, Button, Card } from 'react-bootstrap';
import { Link } from 'react-router-dom';

const HomePage = () => (
  <Container className="py-5">
    <Row className="text-center mb-5">
      <Col>
        <h1 className="display-4 fw-bold text-primary">🛡️ Product Warranty Management</h1>
        <p className="lead text-muted mt-3">
          Manage your products, warranties, and claims in one secure platform
        </p>
      </Col>
    </Row>

    <Row className="g-4 mb-5">
      <Col md={4}>
        <Card className="shadow-sm h-100">
          <Card.Body>
            <h5 className="card-title">📦 Product Management</h5>
            <p className="card-text">Register and manage your products with detailed information</p>
          </Card.Body>
        </Card>
      </Col>
      <Col md={4}>
        <Card className="shadow-sm h-100">
          <Card.Body>
            <h5 className="card-title">✅ Warranty Tracking</h5>
            <p className="card-text">Track warranty status and expiry dates easily</p>
          </Card.Body>
        </Card>
      </Col>
      <Col md={4}>
        <Card className="shadow-sm h-100">
          <Card.Body>
            <h5 className="card-title">📋 Claim Management</h5>
            <p className="card-text">Submit and track warranty claims efficiently</p>
          </Card.Body>
        </Card>
      </Col>
    </Row>

    <Row className="text-center">
      <Col>
        <Link to="/login" className="btn btn-primary btn-lg me-2">Login</Link>
        <Link to="/register" className="btn btn-outline-primary btn-lg">Register</Link>
      </Col>
    </Row>
  </Container>
);

export default HomePage;
