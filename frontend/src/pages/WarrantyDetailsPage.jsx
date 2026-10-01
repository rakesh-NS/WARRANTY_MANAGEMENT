import React, { useState, useEffect } from 'react';
import { Container, Row, Col, Card, Button, Spinner, Alert, Badge } from 'react-bootstrap';
import { Link } from 'react-router-dom';
import { productService, warrantyService } from '../services/serviceApi';

const WarrantyDetailsPage = () => {
  const [warranties, setWarranties] = useState([]);
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    fetchWarrantyData();
  }, []);

  const fetchWarrantyData = async () => {
    try {
      const [warrantyResponse, productResponse] = await Promise.all([
        warrantyService.getAll(),
        productService.getAll(),
      ]);

      const warrantyList = warrantyResponse.data || [];
      const productList = productResponse.data || [];
      const productsById = Object.fromEntries(productList.map((product) => [product.id, product]));

      const enrichedWarranties = warrantyList.map((warranty) => ({
        ...warranty,
        productMeta: productsById[warranty.productId],
      }));

      setWarranties(enrichedWarranties);
      setProducts(productList);
    } catch (err) {
      setError('Failed to load your warranty details');
    } finally {
      setLoading(false);
    }
  };

  const getStatusBadge = (status) => {
    const normalizedStatus = (status || '').toUpperCase();

    if (normalizedStatus === 'ACTIVE') return 'success';
    if (normalizedStatus === 'EXPIRED') return 'danger';
    if (normalizedStatus === 'PENDING') return 'warning';
    return 'secondary';
  };

  if (loading) return <Spinner animation="border" className="d-block mx-auto mt-5" />;

  return (
    <Container className="py-5">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <div className="text-primary fw-semibold">Coverage overview</div>
          <h1 className="mb-0">My warranties</h1>
        </div>
        <Link to="/products" className="btn btn-outline-primary">Back to products</Link>
      </div>

      {error && <Alert variant="danger">{error}</Alert>}

      {!warranties.length ? (
        <Alert variant="light" className="border">
          You do not have any active warranty records yet. Register a product to generate warranty coverage.
        </Alert>
      ) : (
        <Row className="g-4">
          {warranties.map((warranty) => {
            const product = warranty.productMeta || {};
            const status = (warranty.status || '').toUpperCase();
            const isEligibleForClaim = status === 'ACTIVE';

            return (
              <Col lg={6} key={warranty.id}>
                <Card className="h-100 shadow-sm border-0">
                  <Card.Body className="d-flex flex-column">
                    <div className="d-flex justify-content-between align-items-start mb-3">
                      <div>
                        <h5 className="mb-1">{warranty.productName || product.productName || 'Product'}</h5>
                        <div className="text-muted">{product.brand || 'Brand not available'} • {product.model || 'Model not available'}</div>
                      </div>
                      <Badge bg={getStatusBadge(warranty.status)}>{warranty.status || 'UNKNOWN'}</Badge>
                    </div>

                    <div className="small text-muted mb-3">
                      <div><strong>Serial:</strong> {product.serialNumber || 'N/A'}</div>
                      <div><strong>Coverage:</strong> {warranty.startDate} to {warranty.expiryDate}</div>
                      <div><strong>Type:</strong> Standard Warranty</div>
                    </div>

                    <div className="mt-auto pt-3 border-top">
                      <div className="d-flex justify-content-between align-items-center mb-3">
                        <span className={isEligibleForClaim ? 'text-success fw-semibold' : 'text-muted'}>
                          {isEligibleForClaim ? 'Eligible for claim' : 'Not eligible for claim'}
                        </span>
                        <span className="small text-muted">{product.category || 'General category'}</span>
                      </div>

                      <div className="d-flex gap-2 flex-wrap">
                        <Link to={`/claims?productId=${warranty.productId}`} className={`btn btn-sm ${isEligibleForClaim ? 'btn-primary' : 'btn-secondary disabled'}`}>
                          Raise claim
                        </Link>
                        <Link to="/products" className="btn btn-sm btn-outline-secondary">Manage products</Link>
                      </div>
                    </div>
                  </Card.Body>
                </Card>
              </Col>
            );
          })}
        </Row>
      )}
    </Container>
  );
};

export default WarrantyDetailsPage;
