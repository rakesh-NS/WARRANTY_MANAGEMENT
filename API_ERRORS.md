# API Error Codes and Messages

## Success Responses

| Code | Message | Description |
|------|---------|-------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created successfully |
| 204 | No Content | Request successful, no content to return |

## Error Responses

| Code | Error | Description |
|------|-------|-------------|
| 400 | Bad Request | Invalid request parameters |
| 401 | Unauthorized | Authentication required |
| 403 | Forbidden | Access denied |
| 404 | Not Found | Resource not found |
| 409 | Conflict | Duplicate resource (email already exists) |
| 500 | Internal Server Error | Server error |

## Common Error Messages

### Authentication Errors

```json
{
  "timestamp": "2024-01-10T10:30:00",
  "status": 401,
  "error": "Unauthorized",
  "message": "Invalid email or password",
  "path": "/api/users/login"
}
```

### Resource Not Found

```json
{
  "timestamp": "2024-01-10T10:30:00",
  "status": 404,
  "error": "Not Found",
  "message": "User not found with id: 999",
  "path": "/api/users/999"
}
```

### Duplicate Resource

```json
{
  "timestamp": "2024-01-10T10:30:00",
  "status": 409,
  "error": "Conflict",
  "message": "Email already exists",
  "path": "/api/users/register"
}
```

### Validation Error

```json
{
  "email": "Email is required",
  "password": "Password is required",
  "name": "Name is required"
}
```

## Validation Rules

### User Fields
- **name**: Required, max 100 characters
- **email**: Required, must be valid email format
- **password**: Required, min 6 characters
- **phone**: Optional
- **role**: Required (USER/ADMIN)

### Product Fields
- **productName**: Required, max 100 characters
- **brand**: Required, max 100 characters
- **model**: Required, max 100 characters
- **serialNumber**: Required, must be unique
- **purchaseDate**: Required, valid date format
- **price**: Required, must be positive decimal

### Warranty Fields
- **productId**: Required, must exist
- **userId**: Required, must exist
- **startDate**: Required, valid date
- **expiryDate**: Required, must be after startDate
- **status**: Required (Active/Expired/Extended)

### Claim Fields
- **warrantyId**: Required, must exist
- **claimDate**: Required, valid date
- **issueDescription**: Required, min 10 characters
- **status**: Auto-set to "Pending" (can be updated to Approved/Rejected)

## Rate Limiting

Currently not implemented. Can be added using:
- Spring Cloud Netflix Hystrix
- Spring Cloud Config Server
- Custom interceptors
