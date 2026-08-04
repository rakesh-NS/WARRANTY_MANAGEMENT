import React, { useState, useEffect } from 'react';
import { Container, Table, Button, Spinner, Alert, Form, Modal } from 'react-bootstrap';
import { claimService } from '../services/serviceApi';

const ManageClaimsPage = () => {
  const [claims, setClaims] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [selectedClaim, setSelectedClaim] = useState(null);
  const [showModal, setShowModal] = useState(false);
  const [remarks, setRemarks] = useState('');

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

  const handleApprove = async (id) => {
    try {
      await claimService.updateStatus(id, 'Approved', remarks);
      fetchClaims();
      setShowModal(false);
      setRemarks('');
    } catch (err) {
      setError('Failed to approve claim');
    }
  };

  const handleReject = async (id) => {
    try {
      await claimService.updateStatus(id, 'Rejected', remarks);
      fetchClaims();
      setShowModal(false);
      setRemarks('');
    } catch (err) {
      setError('Failed to reject claim');
    }
  };

  if (loading) return <Spinner animation="border" />;

  return (
    <Container className="py-5">
      <h1 className="mb-4">Manage Claims</h1>
      {error && <Alert variant="danger">{error}</Alert>}
      <Table striped bordered hover>
        <thead>
          <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Issue</th>
            <th>Status</th>
            <th>Claim Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {claims.map(c => (
            <tr key={c.id}>
              <td>{c.id}</td>
              <td>{c.productId}</td>
              <td>{c.issueDescription.substring(0, 50)}...</td>
              <td>
                <span className={`badge bg-${
                  c.status === 'Pending' ? 'warning' :
                  c.status === 'Approved' ? 'success' :
                  'danger'
                }`}>
                  {c.status}
                </span>
              </td>
              <td>{c.claimDate}</td>
              <td>
                {c.status === 'Pending' && (
                  <>
                    <button onClick={() => { setSelectedClaim(c); setShowModal(true); }} className="btn btn-sm btn-success me-2">Approve</button>
                    <button onClick={() => { setSelectedClaim(c); setShowModal(true); }} className="btn btn-sm btn-danger">Reject</button>
                  </>
                )}
              </td>
            </tr>
          ))}
        </tbody>
      </Table>

      <Modal show={showModal} onHide={() => setShowModal(false)}>
        <Modal.Header closeButton>
          <Modal.Title>Review Claim</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <Form.Group>
            <Form.Label>Admin Remarks</Form.Label>
            <Form.Control
              as="textarea"
              rows={3}
              value={remarks}
              onChange={(e) => setRemarks(e.target.value)}
            />
          </Form.Group>
        </Modal.Body>
        <Modal.Footer>
          <Button variant="success" onClick={() => handleApprove(selectedClaim?.id)}>Approve</Button>
          <Button variant="danger" onClick={() => handleReject(selectedClaim?.id)}>Reject</Button>
        </Modal.Footer>
      </Modal>
    </Container>
  );
};

export default ManageClaimsPage;
