import React, { useState, useEffect } from 'react';
import { Container, Row, Col, Card, Button, Spinner, Alert, Form, Badge } from 'react-bootstrap';
import { useSearchParams } from 'react-router-dom';
import { claimService, productService } from '../services/serviceApi';

const initialForm = {
  productId: '',
  issueCategory: 'GENERAL',
  issueDescription: '',
};

const ClaimHistoryPage = () => {
  const [claims, setClaims] = useState([]);
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [formData, setFormData] = useState(initialForm);
  const [searchParams] = useSearchParams();

  useEffect(() => {
    const productIdFromUrl = searchParams.get('productId');
    if (productIdFromUrl) {
      setFormData((current) => ({ ...current, productId: productIdFromUrl }));
    }
    fetchClaimData();
  }, [searchParams]);

  const fetchClaimData = async () => {
    try {
      const [claimsResponse, productsResponse] = await Promise.all([
        claimService.getAll(),
        productService.getAll(),
      ]);

      setClaims(claimsResponse.data || []);
      setProducts(productsResponse.data || []);
    } catch (err) {
      setError('Failed to load claims and products');
    } finally {
      setLoading(false);
    }
  };

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await claimService.submit({
        ...formData,
        productId: Number(formData.productId),
      });
      await fetchClaimData();
      setFormData({ ...initialForm, productId: searchParams.get('productId') || '' });
    } catch (err) {
      setError(err.response?.data?.message || 'Unable to submit warranty claim. Please verify product eligibility and try again.');
    }
  };

  const getStatusVariant = (status) => {
    const normalizedStatus = (status || '').toUpperCase();
    if (normalizedStatus === 'PENDING') return 'warning';
    if (normalizedStatus === 'UNDER_REVIEW') return 'info';
    if (normalizedStatus === 'APPROVED' || normalizedStatus === 'REPAIR_IN_PROGRESS' || normalizedStatus === 'RESOLVED') return 'success';
    if (normalizedStatus === 'REJECTED') return 'danger';
    return 'secondary';
  };

  if (loading) return <Spinner animation="border" className="d-block mx-auto mt-5" />;

  return (
    <Container className="py-5">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <div className="text-primary fw-semibold">Claim center</div>
          <h1 className="mb-0">Warranty claims</h1>
        </div>
        <Button variant="outline-primary" onClick={() => setFormData({ ...initialForm, productId: searchParams.get('productId') || '' })}>
          New claim
        </Button>
      </div>

      {error && <Alert variant="danger">{error}</Alert>}

      <Card className="mb-4 shadow-sm border-0">
        <Card.Body className="p-4">
          <h5 className="mb-3">Submit a claim</h5>
          <Form onSubmit={handleSubmit}>
            <Row>
              <Col md={4}>
                <Form.Group className="mb-3">
                  <Form.Label>Product</Form.Label>
                  <Form.Select name="productId" value={formData.productId} onChange={handleChange} required>
                    <option value="">Select product</option>
                    {products.map((product) => (
                      <option key={product.id} value={product.id}>{product.productName} ({product.brand})</option>
                    ))}
                  </Form.Select>
                </Form.Group>
              </Col>
              <Col md={4}>
                <Form.Group className="mb-3">
                  <Form.Label>Issue category</Form.Label>
                  <Form.Select name="issueCategory" value={formData.issueCategory} onChange={handleChange}>
                    <option value="GENERAL">General</option>
                    <option value="ELECTRICAL">Electrical</option>
                    <option value="MECHANICAL">Mechanical</option>
                    <option value="DISPLAY">Display</option>
                    <option value="SOFTWARE">Software</option>
                    <option value="ACCESSORY">Accessory</option>
                  </Form.Select>
                </Form.Group>
              </Col>
              <Col md={4}>
                <Form.Group className="mb-3">
                  <Form.Label>Claim date</Form.Label>
                  <Form.Control type="date" readOnly value={new Date().toISOString().slice(0, 10)} />
                </Form.Group>
              </Col>
            </Row>

            <Form.Group className="mb-3">
              <Form.Label>Describe the problem</Form.Label>
              <Form.Control
                as="textarea"
                rows={4}
                name="issueDescription"
                value={formData.issueDescription}
                onChange={handleChange}
                placeholder="Explain the defect, symptoms, and when it started"
                required
              />
            </Form.Group>

            <div className="d-flex gap-2">
              <Button variant="primary" type="submit">Submit claim</Button>
              <Button variant="outline-secondary" type="button" onClick={() => setFormData({ ...initialForm, productId: searchParams.get('productId') || '' })}>Clear</Button>
            </div>
          </Form>
        </Card.Body>
      </Card>

      <h4 className="mb-3">Claim history</h4>
      {!claims.length ? (
        <Alert variant="light" className="border">
          You have not submitted any warranty claims yet.
        </Alert>
      ) : (
        <Row className="g-4">
          {claims.map((claim) => (
            <Col md={6} key={claim.id}>
              <Card className="h-100 shadow-sm border-0">
                <Card.Body>
                  <div className="d-flex justify-content-between align-items-start mb-2">
                    <div>
                      <h6 className="mb-1">{claim.issueCategory || 'General issue'}</h6>
                      <div className="text-muted small">Claim #{claim.id}</div>
                    </div>
                    <Badge bg={getStatusVariant(claim.status)}>{claim.status || 'PENDING'}</Badge>
                  </div>

                  <p className="mb-3">{claim.issueDescription}</p>

                  <div className="small text-muted">
                    <div><strong>Product ID:</strong> {claim.productId || 'N/A'}</div>
                    <div><strong>Claim date:</strong> {claim.claimDate || 'Not provided'}</div>
                    <div><strong>Admin remarks:</strong> {claim.adminRemarks || 'Waiting for review'}</div>
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

export default ClaimHistoryPage;
