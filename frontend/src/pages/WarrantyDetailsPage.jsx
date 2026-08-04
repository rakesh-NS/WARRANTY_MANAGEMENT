import React, { useState, useEffect } from 'react';
import { Container, Table, Button, Spinner, Alert, Form } from 'react-bootstrap';
import { warrantyService } from '../services/serviceApi';

const WarrantyDetailsPage = () => {
  const [warranties, setWarranties] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [showForm, setShowForm] = useState(false);
  const [formData, setFormData] = useState({
    productId: '',
    startDate: '',
    expiryDate: '',
    status: 'Active',
  });
  const user = JSON.parse(localStorage.getItem('user'));

  useEffect(() => {
    fetchWarranties();
  }, []);

  const fetchWarranties = async () => {
    try {
      const response = await warrantyService.getAll();
      setWarranties(response.data);
    } catch (err) {
      setError('Failed to load warranties');
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
      await warrantyService.create({
        ...formData,
        userId: user.id,
      });
      fetchWarranties();
      setShowForm(false);
      setFormData({ productId: '', startDate: '', expiryDate: '', status: 'Active' });
    } catch (err) {
      setError('Failed to create warranty');
    }
  };

  if (loading) return <Spinner animation="border" />;

  return (
    <Container className="py-5">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h1>Warranties</h1>
        <Button variant="primary" onClick={() => setShowForm(!showForm)}>
          {showForm ? '✕ Cancel' : '+ New Warranty'}
        </Button>
      </div>

      {error && <Alert variant="danger">{error}</Alert>}

      {showForm && (
        <div className="card mb-4 p-4">
          <Form onSubmit={handleSubmit}>
            <Form.Group className="mb-3">
              <Form.Label>Product ID</Form.Label>
              <Form.Control
                type="number"
                name="productId"
                value={formData.productId}
                onChange={handleChange}
                required
              />
            </Form.Group>
            <Form.Group className="mb-3">
              <Form.Label>Start Date</Form.Label>
              <Form.Control
                type="date"
                name="startDate"
                value={formData.startDate}
                onChange={handleChange}
                required
              />
            </Form.Group>
            <Form.Group className="mb-3">
              <Form.Label>Expiry Date</Form.Label>
              <Form.Control
                type="date"
                name="expiryDate"
                value={formData.expiryDate}
                onChange={handleChange}
                required
              />
            </Form.Group>
            <Button variant="success" type="submit">Create Warranty</Button>
          </Form>
        </div>
      )}

      <Table striped bordered hover>
        <thead>
          <tr>
            <th>Product</th>
            <th>Start Date</th>
            <th>Expiry Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          {warranties.map(w => (
            <tr key={w.id}>
              <td>{w.productName}</td>
              <td>{w.startDate}</td>
              <td>{w.expiryDate}</td>
              <td>
                <span className={`badge bg-${w.status === 'Active' ? 'success' : 'danger'}`}>
                  {w.status}
                </span>
              </td>
            </tr>
          ))}
        </tbody>
      </Table>
    </Container>
  );
};

export default WarrantyDetailsPage;
