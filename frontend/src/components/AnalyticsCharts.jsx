import React from 'react';

export const TrendChart = ({ data, color = '#2563eb' }) => {
  const width = 320;
  const height = 150;
  const max = Math.max(...data, 1);
  const stepX = width / (data.length - 1);

  const points = data
    .map((value, index) => {
      const x = index * stepX;
      const y = height - (value / max) * (height - 20) - 10;
      return `${x},${y}`;
    })
    .join(' ');

  return (
    <svg viewBox={`0 0 ${width} ${height}`} className="w-100" role="img" aria-label="service trend chart">
      <defs>
        <linearGradient id="trendFill" x1="0" x2="0" y1="0" y2="1">
          <stop offset="0%" stopColor={color} stopOpacity="0.35" />
          <stop offset="100%" stopColor={color} stopOpacity="0.05" />
        </linearGradient>
      </defs>
      {[0, 1, 2, 3].map((line) => (
        <line
          key={line}
          x1="0"
          x2={width}
          y1={20 + line * 30}
          y2={20 + line * 30}
          stroke="#e5e7eb"
          strokeDasharray="4 4"
        />
      ))}
      <polyline fill="none" stroke={color} strokeWidth="3" points={points} />
      <polygon points={`0,${height} ${points} ${width},${height}`} fill="url(#trendFill)" opacity="0.9" />
      {data.map((value, index) => {
        const x = index * stepX;
        const y = height - (value / max) * (height - 20) - 10;
        return <circle key={index} cx={x} cy={y} r="4" fill={color} />;
      })}
    </svg>
  );
};

export const DonutChart = ({ segments = [] }) => {
  const size = 120;
  const radius = 42;
  const circumference = 2 * Math.PI * radius;
  const total = segments.reduce((sum, segment) => sum + segment.value, 0) || 1;

  let offset = 0;

  return (
    <div className="d-flex align-items-center justify-content-center gap-3 flex-wrap">
      <svg width={size} height={size} viewBox="0 0 120 120" role="img" aria-label="claim distribution chart">
        <circle cx="60" cy="60" r={radius} fill="none" stroke="#e5e7eb" strokeWidth="16" />
        {segments.map((segment, index) => {
          const segmentLength = (segment.value / total) * circumference;
          const dashOffset = -offset;
          offset += segmentLength;

          return (
            <circle
              key={segment.label}
              cx="60"
              cy="60"
              r={radius}
              fill="none"
              stroke={segment.color}
              strokeWidth="16"
              strokeDasharray={`${segmentLength} ${circumference - segmentLength}`}
              strokeDashoffset={dashOffset}
              transform="rotate(-90 60 60)"
              strokeLinecap="round"
            />
          );
        })}
        <text x="60" y="57" textAnchor="middle" fontSize="18" fontWeight="700" fill="#1f2937">
          {total}
        </text>
        <text x="60" y="75" textAnchor="middle" fontSize="10" fill="#64748b">
          cases
        </text>
      </svg>
      <div className="d-flex flex-column gap-2">
        {segments.map((segment) => (
          <div key={segment.label} className="d-flex align-items-center gap-2 text-sm">
            <span className="rounded-circle d-inline-block" style={{ width: 10, height: 10, background: segment.color }} />
            <span>{segment.label}</span>
            <strong>{segment.value}</strong>
          </div>
        ))}
      </div>
    </div>
  );
};

export const MiniStatBars = ({ data = [] }) => (
  <div className="d-flex align-items-end gap-2 mt-3" style={{ height: 100 }}>
    {data.map((item, index) => (
      <div key={index} className="d-flex flex-column align-items-center gap-2" style={{ flex: 1 }}>
        <div
          className="w-100 rounded-top"
          style={{
            height: `${item.value}%`,
            background: item.color || '#60a5fa',
            minHeight: 18,
            boxShadow: 'inset 0 -8px 12px rgba(255,255,255,0.18)'
          }}
        />
        <small className="text-muted">{item.label}</small>
      </div>
    ))}
  </div>
);
