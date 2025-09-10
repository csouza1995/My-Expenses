# 🚀 REST API - Expense Management System

## 📋 **Overview**

RESTful API for personal expense management with JWT authentication. 
Users can register and login directly through the API endpoints.

**Base URL:** `http://localhost:8080/api`

## 🔐 **Authentication**

### Register New User
```http
POST /api/auth/register
Content-Type: application/json

{
    "name": "string",
    "email": "string",
    "password": "string"
}
```

**Success Response (201):**
```json
{
    "success": true,
    "message": "Registration successful",
    "data": {
        "token": "jwt_token_here",
        "user": {
            "id": 1,
            "username": "user",
            "email": "user@example.com"
        }
    }
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["This email address has already been taken."],
        "password": ["Password should contain at least 6 characters."]
    }
}
```

### Login (Existing Users)
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "string",
    "password": "string"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "token": "jwt_token_here",
        "user": {
            "id": 1,
            "username": "user",
            "email": "user@example.com"
        }
    }
}
```

### Verify Token
```http
GET /api/auth/verify
Authorization: Bearer {jwt_token}
```

### Refresh Token
```http
POST /api/auth/refresh
Authorization: Bearer {jwt_token}
```

---

## 💰 **Expense Management**

> **Note:** All expense routes require JWT authentication in header: `Authorization: Bearer {token}`

### List Expenses
```http
GET /api/expense
Authorization: Bearer {jwt_token}
```

**Advanced Filtering Examples:**

#### 🔍 **Filter by Description**
```http
GET /api/expense?description=lunch
```
Search for expenses containing "lunch" in description.

#### 🏷️ **Filter by Category**
```http
GET /api/expense?category=1
```
Get only "Alimentação" category expenses.

#### 📅 **Filter by Date Range**
```http
GET /api/expense?date_from=2025-01-01&date_to=2025-12-31
```
Get expenses from specific period.

#### 💵 **Filter by Value Range**
```http
GET /api/expense?value_min=10.00&value_max=100.00
```
Get expenses between $10.00 and $100.00.

#### 🔗 **Multiple Filters Combined**
```http
GET /api/expense?category=1&date_from=2025-09-01&date_to=2025-09-30&description=restaurant
```
Get food expenses from September 2025 containing "restaurant".

#### 📄 **Pagination Control**
```http
GET /api/expense?page=2&per_page=5
```
Get page 2 with 5 expenses per page.

#### 📊 **Sorting Options**
```http
GET /api/expense?sort=date&order=desc
```
Sort by date in descending order.

Available sort fields: `date`, `value`, `description`, `created_at`
Order options: `asc`, `desc`

**Success Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "expenses": [
            {
                "id": 1,
                "description": "Restaurant lunch",
                "category": 1,
                "category_name": "Alimentação",
                "value": 25.50,
                "date": "2025-09-10",
                "date_formatted": "10/09/2025",
                "created_at": 1725955800,
                "updated_at": 1725955800
            },
            {
                "id": 2,
                "description": "Coffee shop",
                "category": 1,
                "category_name": "Alimentação",
                "value": 8.75,
                "date": "2025-09-09",
                "date_formatted": "09/09/2025",
                "created_at": 1725869400,
                "updated_at": 1725869400
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 10,
            "total_count": 15,
            "total_pages": 2
        }
    }
}
```

### View Single Expense
```http
GET /api/expense/{id}
Authorization: Bearer {jwt_token}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Success",
    "data": {
        "expense": {
            "id": 1,
            "description": "Restaurant lunch",
            "category": 1,
            "category_name": "Alimentação",
            "value": 25.50,
            "date": "2025-09-10",
            "date_formatted": "10/09/2025",
            "created_at": 1725955800,
            "updated_at": 1725955800
        }
    }
}
```

### Create Expense
```http
POST /api/expense
Authorization: Bearer {jwt_token}
Content-Type: application/json

{
    "description": "string",
    "category": 1,
    "value": 25.50,
    "date": "2025-09-10"
}
```

**Alternative using "value" field:**
```json
{
    "description": "string",
    "category": 1,
    "value": 25.50,
    "date": "2025-09-10"
}
```

**Available Categories:**
- `0` - Outros
- `1` - Alimentação
- `2` - Transporte  
- `3` - Moradia
- `4` - Saúde
- `5` - Lazer

**Success Response (201):**
```json
{
    "success": true,
    "message": "Expense created successfully",
    "data": {
        "expense": {
            "id": 15,
            "description": "Restaurant lunch",
            "category": 1,
            "category_name": "Alimentação",
            "value": 25.50,
            "date": "2025-09-10",
            "date_formatted": "10/09/2025",
            "created_at": 1725955800,
            "updated_at": 1725955800
        }
    }
}
```

### Update Expense
```http
PUT /api/expense/{id}
Authorization: Bearer {jwt_token}
Content-Type: application/json

{
    "description": "string",
    "category": 1,
    "value": 30.00,
    "date": "2025-09-10"
}
```

### Delete Expense
```http
DELETE /api/expense/{id}
Authorization: Bearer {jwt_token}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Expense deleted successfully"
}
```

---

## 📊 **Advanced Query Examples**

### Complex Filtering Scenarios

#### Monthly Report
```http
GET /api/expense?date_from=2025-09-01&date_to=2025-09-30&sort=date&order=desc
```

#### High-Value Expenses
```http
GET /api/expense?value_min=100.00&sort=value&order=desc
```

#### Food Expenses This Week
```http
GET /api/expense?category=1&date_from=2025-09-04&date_to=2025-09-10
```

#### Search and Filter
```http
GET /api/expense?description=uber&category=2&date_from=2025-09-01
```

#### Recent Expenses (Last 7 days)
```http
GET /api/expense?date_from=2025-09-03&sort=created_at&order=desc&per_page=20
```

---

## 🔒 **Security**

### JWT Authentication
- **Algorithm:** HS256
- **Expiration:** 1 hour
- **Header:** `Authorization: Bearer {token}`

### Data Isolation
- Users can only access their own expenses
- Automatic ownership verification in all operations

### Input Validation
- Sanitization of all parameters
- Strict data type validation
- Protection against SQL Injection and XSS

---

## 📊 **HTTP Status Codes**

| Code | Meaning |
|------|---------|
| 200 | OK - Success |
| 201 | Created - Successfully created |
| 400 | Bad Request - Invalid parameters |
| 401 | Unauthorized - Invalid/missing token |
| 404 | Not Found - Resource not found |
| 409 | Conflict - User already exists |
| 422 | Unprocessable Entity - Validation error |
| 500 | Internal Server Error - Internal error |

---

## 🧪 **Testing the API**

### Using cURL

**Register New User:**
```bash
curl -X POST http://localhost:8080/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"SecurePass123"}'
```

**Login:**
```bash
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"SecurePass123"}'
```

**Create Expense:**
```bash
curl -X POST http://localhost:8080/api/expense \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{"description":"Lunch","category":1,"value":25.50,"date":"2025-09-10"}'
```

**List Expenses with Filters:**
```bash
curl -X GET "http://localhost:8080/api/expense?category=1&date_from=2025-09-01&date_to=2025-09-30" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Test Interface
Open the `api_test.html` file in your browser for a visual testing interface.

---

## ⚡ **Additional Features**

### CORS
- Configured to allow access from any origin
- Appropriate headers for development

### Pagination
- Automatic in listings
- Complete pagination information in response

### Filters
- Multiple filters via query parameters
- Search by description, category, period, value range

### Formatting
- Dates in Brazilian format (DD/MM/YYYY)
- Appropriate numeric values for frontend
- Consistent response structure

### Error Handling
- Detailed validation errors
- Consistent error format
- Appropriate HTTP status codes

---

## 🎯 **Usage Examples**

### Complete Workflow
1. **Register user** via web interface
2. **Login via API** → Receive token
3. **Use token** in all subsequent requests
4. **Create expenses** with valid data
5. **List and filter** expenses
6. **Update/Delete** as needed

### Error Handling
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "description": ["Description cannot be blank."],
        "value": ["Value must be greater than 0."]
    }
}
```

### Filter Combinations
You can combine any filters for powerful queries:

```bash
# Food expenses over $20 from last month, sorted by value
GET /api/expense?category=1&value_min=20.00&date_from=2025-08-01&date_to=2025-08-31&sort=value&order=desc

# Search for "restaurant" expenses in the last 30 days
GET /api/expense?description=restaurant&date_from=2025-08-11&sort=date&order=desc

# Paginated results with custom page size
GET /api/expense?page=3&per_page=15&sort=created_at&order=desc
```

**🚀 Complete API Ready for Use!**

