# Sample Code — Laravel Backend Examples

This repository contains **clean, reusable Laravel backend code samples** following best practices like Service pattern, Form Requests (validation), Resources/Transformers, and structured API responses.  
It’s meant as a demonstration of my style and coding standards.

---

## 📦 Contents
This Contain chunks of code from my two different projects.

---

## 🧩 Architectural Principles & Patterns

Here are the key design decisions and patterns demonstrated in this repo:

- **Service Layer** — All business logic lives in service classes, keeping controllers thin and focused.  
- **Form Requests / Validator Classes** — Validation logic is separated using Laravel’s FormRequest or custom validator classes instead of bloating controllers.  
- **API Resources / Transformers** — Responses are shaped consistently using Laravel Resources / Transformers.  
- **ApiResponse Trait** — Uniform JSON success / error response structure across controllers.  
- **Dependency Injection** — Services are injected into controllers for cleaner code and easier testing.  
- **Exception Handling** — Basic try/catch in controllers (you can expand with global exception handlers)  
- **Swagger / OpenAPI Annotations** — Some endpoints include annotations for auto-generated API documentation.

---