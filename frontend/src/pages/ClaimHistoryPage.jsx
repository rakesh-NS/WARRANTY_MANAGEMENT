import React, { useState, useEffect } from 'react';
import { Container, Table, Button, Spinner, Alert, Form, Modal } from 'react-bootstrap';
import { claimService } from '../services/serviceApi';

const ClaimHistoryPage = () => {
  const [claims, setClaims] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [showForm, setShowForm] = useState(false);
  const [formData, setFormData] = useState({
    warrantyId: '',
    claimDate: '',
    issueDescription: '',
  });
  const user = JSON.parse(localStorage.getItem('user'));

  useEffect(() => {
    fetchClaims();
  }, []);

  const fetchClaims = async () => {
    try {
      const response = await claimService.getAll();
      setClaims(response.data);
    } catch (err) {
      setError('Failed to load claims');
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
      await claimService.submit(formData);
      fetchClaims();
      setShowForm(false);
      setFormData({ warrantyId: '', claimDate: '', issueDescription: '' });
    } catch (err) {
      setError('Failed to submit claim');
    }
  };

  if (loading) return <Spinner animation="border" />;

  return (
    <Container className="py-5">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h1>Warranty Claims</h1>
        <Button variant="primary" onClick={() => setShowForm(!showForm)}>
          {showForm ? '✕ Cancel' : '+ Submit Claim'}
        </Button>
      </div>

      {error && <Alert variant="danger">{error}</Alert>}

      {showForm && (
        <div className="card mb-4 p-4">
          <Form onSubmit={handleSubmit}>
            <Form.Group className="mb-3">
              <Form.Label>Warranty ID</Form.Label>
              <Form.Control
                type="number"
                name="warrantyId"
                value={formData.warrantyId}
                onChange={handleChange}
                required
              />
            </Form.Group>
            <Form.Group className="mb-3">
              <Form.Label>Claim Date</Form.Label>
              <Form.Control
                type="date"
                name="claimDate"
                value={formData.claimDate}
                onChange={handleChange}
                required
              />
            </Form.Group>
            <Form.Group className="mb-3">
              <Form.Label>Issue Description</Form.Label>
              <Form.Control
                as="textarea"
                rows={4}
                name="issueDescription"
                value={formData.issueDescription}
                onChange={handleChange}
                required
              />
            </Form.Group>
            <Button variant="success" type="submit">Submit Claim</Button>
          </Form>
        </div>
      )}

      <Table striped bordered hover>
        <thead>
          <tr>
            <th>ID</th>
            <th>Issue</th>
            <th>Claim Date</th>
            <th>Status</th>
            <th>Admin Remarks</th>
          </tr>
        </thead>
        <tbody>
          {claims.map(c => (
            <tr key={c.id}>
              <td>{c.id}</td>
              <td>{c.issueDescription}</td>
              <td>{c.claimDate}</td>
              <td>
                <span className={`badge bg-${
                  c.status === 'Pending' ? 'warning' :
                  c.status === 'Approved' ? 'success' :
                  'danger'
                }`}>
                  {c.status}
                </span>
              </td>
              <td>{c.adminRemarks || '-'}</td>
            </tr>
          ))}
        </tbody>
      </Table>
    </Container>
  );
};

export default ClaimHistoryPage;
