import React, { useState, useEffect } from 'react';
import { Container, Row, Col, Card, Spinner, Badge, ProgressBar } from 'react-bootstrap';
import { adminService } from '../services/serviceApi';
import { TrendChart, DonutChart, MiniStatBars } from '../components/AnalyticsCharts';

const adminCards = [
  { key: 'totalUsers', label: 'Total Users', color: 'primary', icon: '👥' },
  { key: 'totalProducts', label: 'Products', color: 'success', icon: '📦' },
  { key: 'activeWarranties', label: 'Active Warranty', color: 'info', icon: '✅' },
  { key: 'expiredWarranties', label: 'Expired', color: 'warning', icon: '⚠️' },
  { key: 'pendingClaims', label: 'Pending Claims', color: 'danger', icon: '⏳' },
  { key: 'approvedClaims', label: 'Approved', color: 'success', icon: '🎯' },
];

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

  if (loading) return <Spinner animation="border" className="d-block mx-auto my-5" />;
  if (!stats) return <div className="text-center py-5">Unable to load dashboard data.</div>;

  const claimData = [
    { label: 'Pending', value: Number(stats.pendingClaims || 0), color: '#f59e0b' },
    { label: 'Approved', value: Number(stats.approvedClaims || 0), color: '#22c55e' },
    { label: 'Rejected', value: Number(stats.rejectedClaims || 0), color: '#ef4444' },
  ];

  const trendData = [12, 18, 16, 24, 28, 32, 36, 34, 42, 46, 50, 54];
  const monthlyData = [
    { label: 'Jan', value: 32, color: '#60a5fa' },
    { label: 'Feb', value: 48, color: '#34d399' },
    { label: 'Mar', value: 40, color: '#fbbf24' },
    { label: 'Apr', value: 58, color: '#a78bfa' },
    { label: 'May', value: 72, color: '#fb7185' },
  ];

  const completionRate = Math.min(100, Math.round(((Number(stats.approvedClaims || 0) + Number(stats.pendingClaims || 0)) / Math.max(Number(stats.totalProducts || 1), 1)) * 100));

  return (
    <Container className="py-5">
      <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
          <p className="text-uppercase text-muted mb-1 fw-semibold">Overview</p>
          <h1 className="mb-0">Admin Dashboard</h1>
        </div>
        <Badge bg="primary" className="px-3 py-2">Live monitoring</Badge>
      </div>

      <Row className="g-4 mb-4">
        {adminCards.map((card) => (
          <Col md={6} lg={4} key={card.key}>
            <Card className="shadow-sm border-0 h-100 analytics-card">
              <Card.Body className="p-4">
                <div className="d-flex justify-content-between align-items-start mb-3">
                  <div>
                    <small className="text-muted text-uppercase">{card.label}</small>
                    <h3 className={`mt-2 text-${card.color}`}>{stats[card.key]}</h3>
                  </div>
                  <div className="fs-3">{card.icon}</div>
                </div>
                <ProgressBar now={Math.min(100, Number(stats[card.key] || 0) * 10)} variant={card.color} className="rounded-pill" />
              </Card.Body>
            </Card>
          </Col>
        ))}
      </Row>

      <Row className="g-4 mb-4">
        <Col lg={8}>
          <Card className="shadow-sm border-0 h-100">
            <Card.Body className="p-4">
              <div className="d-flex justify-content-between align-items-center mb-3">
                <h4 className="mb-0">Service trend</h4>
                <Badge bg="light" text="dark">Last 12 months</Badge>
              </div>
              <TrendChart data={trendData} color="#2563eb" />
            </Card.Body>
          </Card>
        </Col>
        <Col lg={4}>
          <Card className="shadow-sm border-0 h-100">
            <Card.Body className="p-4">
              <h4 className="mb-3">Claim distribution</h4>
              <DonutChart segments={claimData} />
            </Card.Body>
          </Card>
        </Col>
      </Row>

      <Row className="g-4">
        <Col lg={6}>
          <Card className="shadow-sm border-0 h-100">
            <Card.Body className="p-4">
              <h4 className="mb-3">Monthly performance</h4>
              <MiniStatBars data={monthlyData} />
            </Card.Body>
          </Card>
        </Col>
        <Col lg={6}>
          <Card className="shadow-sm border-0 h-100">
            <Card.Body className="p-4">
              <h4 className="mb-3">Operational summary</h4>
              <div className="mb-3">
                <div className="d-flex justify-content-between mb-1">
                  <span>Resolution progress</span>
                  <strong>{completionRate}%</strong>
                </div>
                <ProgressBar now={completionRate} variant="success" />
              </div>
              <ul className="list-unstyled mb-0">
                <li className="mb-2">• {stats.totalProducts} active products in database</li>
                <li className="mb-2">• {stats.activeWarranties} current active warranties</li>
                <li className="mb-2">• {stats.pendingClaims} claims awaiting review</li>
                <li>• {stats.rejectedClaims} rejected cases require follow-up</li>
              </ul>
            </Card.Body>
          </Card>
        </Col>
      </Row>
    </Container>
  );
};

export default AdminDashboardPage;
