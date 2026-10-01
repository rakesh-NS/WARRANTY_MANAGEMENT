import React from 'react';
import { Container, Row, Col, Card, Badge } from 'react-bootstrap';
import { Link } from 'react-router-dom';

const features = [
  { icon: '📦', title: 'Product Management', text: 'Track inventory, serial numbers, and purchase details in one place.' },
  { icon: '✅', title: 'Warranty Tracking', text: 'Stay ahead of expiry dates with clean, simple status updates.' },
  { icon: '📋', title: 'Claim Management', text: 'Submit issues, monitor progress, and keep every customer request visible.' },
];

const HomePage = () => (
  <Container className="py-5">
    <Row className="align-items-center mb-5">
      <Col lg={7} className="mb-4 mb-lg-0">
        <Badge bg="primary" className="mb-3 px-3 py-2">Local-first demo workspace</Badge>
        <h1 className="display-5 fw-bold text-primary mb-3">🛡️ Product Warranty Management</h1>
        <p className="lead text-muted mb-4">
          Manage products, warranties, and service claims with a dashboard designed for quick decisions and better customer support.
        </p>
        <div className="d-flex flex-wrap gap-3">
          <Link to="/register" className="btn btn-primary btn-lg">Create account</Link>
          <Link to="/login" className="btn btn-outline-primary btn-lg">Login</Link>
        </div>
      </Col>
      <Col lg={5}>
        <Card className="border-0 shadow-lg">
          <Card.Body className="p-4">
            <div className="status-pill mb-3">● Live support overview</div>
            <h4 className="mb-3">This month</h4>
            <div className="d-flex justify-content-between mb-2">
              <span>Active warranties</span>
              <strong>128</strong>
            </div>
            <div className="d-flex justify-content-between mb-2">
              <span>Pending claims</span>
              <strong>18</strong>
            </div>
            <div className="d-flex justify-content-between mb-2">
              <span>Resolved this week</span>
              <strong>24</strong>
            </div>
            <div className="progress mt-3" style={{ height: '10px' }}>
              <div className="progress-bar bg-success" style={{ width: '76%' }}></div>
            </div>
          </Card.Body>
        </Card>
      </Col>
    </Row>

    <Row className="g-4 mb-5">
      {features.map((feature) => (
        <Col md={4} key={feature.title}>
          <Card className="feature-card h-100 border-0">
            <Card.Body className="p-4">
              <div className="display-6 mb-3">{feature.icon}</div>
              <h5 className="card-title mb-2">{feature.title}</h5>
              <p className="card-text text-muted mb-0">{feature.text}</p>
            </Card.Body>
          </Card>
        </Col>
      ))}
    </Row>
  </Container>
);

export default HomePage;
