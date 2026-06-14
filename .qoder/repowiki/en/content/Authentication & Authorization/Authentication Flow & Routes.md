# Authentication Flow & Routes

<cite>
**Referenced Files in This Document**
- [routes/web.php](file://routes/web.php)
- [routes/auth.php](file://routes/auth.php)
- [routes/api.php](file://routes/api.php)
- [config/auth.php](file://config/auth.php)
- [config/sanctum.php](file://config/sanctum.php)
- [config/session.php](file://config/session.php)
- [app/Http/Middleware/PwaAuth.php](file://app/Http/Middleware/PwaAuth.php)
- [app/Http/Middleware/EnsureRole.php](file://app/Http/Middleware/EnsureRole.php)
- [app/Http/Middleware/SessionTimeout.php](file://app/Http/Middleware/SessionTimeout.php)
- [app/Livewire/Forms/LoginForm.php](file://app/Livewire/Forms/LoginForm.php)
- [app/Livewire/Actions/Logout.php](file://app/Livewire/Actions/Logout.php)
- [resources/views/livewire/pages/auth/login.blade.php](file://resources/views/livewire/pages/auth/login.blade.php)
- [resources/views/components/auth-session-status.blade.php](file://resources/views/components/auth-session-status.blade.php)
- [resources/views/components/input-error.blade.php](file://resources/views/components/input-error.blade.php)
- [public/js/pwa.js](file://public/js/pwa.js)
- [public/sw.js](file://public/sw.js)
- [public/manifest.json](file://public/manifest.json)
- [app/Models/User.php](file://app/Models/User.php)
- [app/Models/PwaToken.php](file://app/Models/PwaToken.php)
- [database/migrations/2026_06_01_010827_create_pwa_tokens_table.php](file://database/migrations/2026_06_01_010827_create_pwa_tokens_table.php)
- [database/migrations/2026_06_01_010828_create_remember_tokens_table.php](file://database/migrations/2026_06_01_010828_create_remember_tokens_table.php)
- [app/Jobs/ProcessPwaSyncJob.php](file://app/Jobs/ProcessPwaSyncJob.php)
</cite>

## Table of Contents
1. [Introduction](#introduction)
2. [Project Structure](#project-structure)
3. [Core Components](#core-components)
4. [Architecture Overview](#architecture-overview)
5. [Detailed Component Analysis](#detailed-component-analysis)
6. [Dependency Analysis](#dependency-analysis)
7. [Performance Considerations](#performance-considerations)
8. [Troubleshooting Guide](#troubleshooting-guide)
9. [Conclusion](#conclusion)

## Introduction
This document provides comprehensive documentation for the authentication flow and routing system in RaporKM Laravel. It covers the complete authentication journey from initial login through session establishment and protected resource access. The documentation details route definitions for authentication endpoints (login, logout, registration, and password reset), explains the separation between web and API authentication flows (including PWA functionality), and documents email verification, password reset workflows, and account confirmation procedures. It also includes examples of middleware application, route protection patterns, redirect handling, integration between traditional web authentication and modern PWA authentication, error handling, validation responses, and user feedback mechanisms.

## Project Structure
The authentication system spans routing, middleware, Livewire components, configuration, models, and PWA assets. Key areas include:
- Route definitions for web and API authentication
- Authentication middleware for role enforcement and PWA-specific checks
- Livewire forms and actions for login/logout
- Configuration for authentication guards, password reset, and Sanctum
- PWA token model and synchronization job
- Frontend components for user feedback and PWA integration

```mermaid
graph TB
subgraph "Routing"
WEB["routes/web.php"]
AUTH["routes/auth.php"]
API["routes/api.php"]
end
subgraph "Middleware"
PWA["PwaAuth.php"]
ROLE["EnsureRole.php"]
TIMEOUT["SessionTimeout.php"]
end
subgraph "Livewire"
LOGIN_FORM["LoginForm.php"]
LOGOUT_ACTION["Logout.php"]
end
subgraph "Config"
AUTH_CONF["config/auth.php"]
SANCTUM["config/sanctum.php"]
SESSION["config/session.php"]
end
subgraph "Models"
USER["User.php"]
PWA_TOKEN["PwaToken.php"]
end
subgraph "PWA Assets"
JS["public/js/pwa.js"]
SW["public/sw.js"]
MANIFEST["public/manifest.json"]
end
WEB --> AUTH
WEB --> LOGIN_FORM
WEB --> LOGOUT_ACTION
AUTH --> PWA
AUTH --> ROLE
AUTH --> TIMEOUT
AUTH_CONF --> PWA
SANCTUM --> API
SESSION --> AUTH
USER --> PWA_TOKEN
JS --> PWA
SW --> PWA
MANIFEST --> PWA
```

**Diagram sources**
- [routes/web.php](file://routes/web.php)
- [routes/auth.php](file://routes/auth.php)
- [routes/api.php](file://routes/api.php)
- [app/Http/Middleware/PwaAuth.php](file://app/Http/Middleware/PwaAuth.php)
- [app/Http/Middleware/EnsureRole.php](file://app/Http/Middleware/EnsureRole.php)
- [app/Http/Middleware/SessionTimeout.php](file://app/Http/Middleware/SessionTimeout.php)
- [app/Livewire/Forms/LoginForm.php](file://app/Livewire/Forms/LoginForm.php)
- [app/Livewire/Actions/Logout.php](file://app/Livewire/Actions/Logout.php)
- [config/auth.php](file://config/auth.php)
- [config/sanctum.php](file://config/sanctum.php)
- [config/session.php](file://config/session.php)
- [app/Models/User.php](file://app/Models/User.php)
- [app/Models/PwaToken.php](file://app/Models/PwaToken.php)
- [public/js/pwa.js](file://public/js/pwa.js)
- [public/sw.js](file://public/sw.js)
- [public/manifest.json](file://public/manifest.json)

**Section sources**
- [routes/web.php](file://routes/web.php)
- [routes/auth.php](file://routes/auth.php)
- [routes/api.php](file://routes/api.php)
- [config/auth.php](file://config/auth.php)
- [config/sanctum.php](file://config/sanctum.php)
- [config/session.php](file://config/session.php)

## Core Components
- Authentication routes: Define endpoints for login, logout, registration, password reset, and email verification.
- Middleware: Enforce role-based access, PWA authentication, and session timeout policies.
- Livewire forms/actions: Provide reactive login/logout flows integrated with Blade components.
- Configuration: Guards, password broker, Sanctum tokens, and session settings.
- PWA integration: Token-based authentication and service worker for offline-capable experiences.
- Feedback components: Display authentication status and validation errors.

**Section sources**
- [routes/auth.php](file://routes/auth.php)
- [app/Http/Middleware/PwaAuth.php](file://app/Http/Middleware/PwaAuth.php)
- [app/Http/Middleware/EnsureRole.php](file://app/Http/Middleware/EnsureRole.php)
- [app/Http/Middleware/SessionTimeout.php](file://app/Http/Middleware/SessionTimeout.php)
- [app/Livewire/Forms/LoginForm.php](file://app/Livewire/Forms/LoginForm.php)
- [app/Livewire/Actions/Logout.php](file://app/Livewire/Actions/Logout.php)
- [config/auth.php](file://config/auth.php)
- [config/sanctum.php](file://config/sanctum.php)
- [config/session.php](file://config/session.php)
- [public/js/pwa.js](file://public/js/pwa.js)

## Architecture Overview
The authentication architecture separates concerns between web and API flows while supporting PWA capabilities. Web authentication relies on sessions and CSRF protection, while API authentication leverages Sanctum tokens. PWA authentication extends API flows with persistent tokens stored via the PWA token model.

```mermaid
sequenceDiagram
participant Client as "Browser/App"
participant WebRoutes as "Web Routes"
participant AuthMW as "Auth Middleware"
participant PwaMW as "PwaAuth Middleware"
participant RoleMW as "EnsureRole Middleware"
participant LoginForm as "LoginForm (Livewire)"
participant Session as "Session Store"
participant Sanctum as "Sanctum Tokens"
participant PwaToken as "PwaToken Model"
Client->>WebRoutes : "GET /login"
WebRoutes->>LoginForm : "Render login form"
Client->>WebRoutes : "POST /login"
WebRoutes->>AuthMW : "Authenticate credentials"
AuthMW->>Session : "Create session"
AuthMW-->>Client : "Redirect to dashboard"
Client->>WebRoutes : "GET /dashboard"
WebRoutes->>RoleMW : "Check role permissions"
RoleMW-->>Client : "Allow or redirect"
Client->>WebRoutes : "GET /logout"
WebRoutes->>Session : "Invalidate session"
WebRoutes-->>Client : "Redirect to login"
Client->>Sanctum : "POST /sanctum/csrf-cookie"
Sanctum-->>Client : "CSRF cookie set"
Client->>PwaMW : "PWA requests with token"
PwaMW->>PwaToken : "Validate token"
PwaMW-->>Client : "Authorized or 401"
```

**Diagram sources**
- [routes/web.php](file://routes/web.php)
- [routes/auth.php](file://routes/auth.php)
- [app/Http/Middleware/PwaAuth.php](file://app/Http/Middleware/PwaAuth.php)
- [app/Http/Middleware/EnsureRole.php](file://app/Http/Middleware/EnsureRole.php)
- [app/Http/Middleware/SessionTimeout.php](file://app/Http/Middleware/SessionTimeout.php)
- [app/Livewire/Forms/LoginForm.php](file://app/Livewire/Forms/LoginForm.php)
- [app/Models/PwaToken.php](file://app/Models/PwaToken.php)
- [config/sanctum.php](file://config/sanctum.php)

## Detailed Component Analysis

### Web Authentication Flow
The web authentication flow uses session-based authentication with middleware for role enforcement and session timeout. The typical journey includes:
- Login page rendering via Livewire form
- Credential submission processed by authentication middleware
- Session creation and redirect to dashboard
- Role-based route protection
- Logout invalidating the session

```mermaid
sequenceDiagram
participant Browser as "Browser"
participant Routes as "routes/web.php"
participant MW as "Auth Middleware"
participant RoleMW as "EnsureRole Middleware"
participant Session as "Session Store"
participant Dashboard as "Protected Resource"
Browser->>Routes : "GET /login"
Routes-->>Browser : "Render login view"
Browser->>Routes : "POST /login"
Routes->>MW : "Validate credentials"
MW->>Session : "Store user session"
MW-->>Browser : "Redirect to /dashboard"
Browser->>Routes : "GET /dashboard"
Routes->>RoleMW : "Check role"
RoleMW->>Dashboard : "Serve protected content"
RoleMW-->>Browser : "Allow or redirect"
Browser->>Routes : "GET /logout"
Routes->>Session : "Clear session"
Routes-->>Browser : "Redirect to /login"
```

**Diagram sources**
- [routes/web.php](file://routes/web.php)
- [app/Http/Middleware/EnsureRole.php](file://app/Http/Middleware/EnsureRole.php)
- [app/Http/Middleware/SessionTimeout.php](file://app/Http/Middleware/SessionTimeout.php)
- [app/Livewire/Forms/LoginForm.php](file://app/Livewire/Forms/LoginForm.php)
- [app/Livewire/Actions/Logout.php](file://app/Livewire/Actions/Logout.php)

**Section sources**
- [routes/web.php](file://routes/web.php)
- [app/Http/Middleware/EnsureRole.php](file://app/Http/Middleware/EnsureRole.php)
- [app/Http/Middleware/SessionTimeout.php](file://app/Http/Middleware/SessionTimeout.php)
- [app/Livewire/Forms/LoginForm.php](file://app/Livewire/Forms/LoginForm.php)
- [app/Livewire/Actions/Logout.php](file://app/Livewire/Actions/Logout.php)

### API Authentication Flow (Sanctum)
API authentication uses Laravel Sanctum for stateless token-based authentication. The flow includes:
- Requesting CSRF cookie for cross-site requests
- Authenticating via API endpoints
- Returning authenticated responses with token scopes

```mermaid
sequenceDiagram
participant Client as "Client"
participant Sanctum as "Sanctum"
participant API as "routes/api.php"
participant Guard as "config/auth.php"
participant Tokens as "Personal Access Tokens"
Client->>Sanctum : "POST /sanctum/csrf-cookie"
Sanctum-->>Client : "Set CSRF cookie"
Client->>API : "POST /api/login"
API->>Guard : "Authenticate user"
Guard->>Tokens : "Issue token"
Guard-->>API : "Authenticated user"
API-->>Client : "200 OK with token"
```

**Diagram sources**
- [routes/api.php](file://routes/api.php)
- [config/sanctum.php](file://config/sanctum.php)
- [config/auth.php](file://config/auth.php)

**Section sources**
- [routes/api.php](file://routes/api.php)
- [config/sanctum.php](file://config/sanctum.php)
- [config/auth.php](file://config/auth.php)

### PWA Authentication Flow
PWA authentication extends API flows with persistent token storage and offline capabilities:
- Service worker enables offline access
- Token persistence via PWA token model
- Background synchronization jobs
- Manifest and app shell support

```mermaid
sequenceDiagram
participant PWA as "PWA Client"
participant SW as "Service Worker (sw.js)"
participant API as "routes/api.php"
participant PwaMW as "PwaAuth Middleware"
participant PwaToken as "PwaToken Model"
participant Job as "ProcessPwaSyncJob"
PWA->>SW : "Install/update"
SW-->>PWA : "App shell ready"
PWA->>API : "Fetch data with token"
API->>PwaMW : "Validate token"
PwaMW->>PwaToken : "Lookup token"
PwaMW-->>API : "Authorized or 401"
API-->>PWA : "Data response"
PWA->>Job : "Queue sync job"
Job-->>PWA : "Sync completion"
```

**Diagram sources**
- [public/sw.js](file://public/sw.js)
- [routes/api.php](file://routes/api.php)
- [app/Http/Middleware/PwaAuth.php](file://app/Http/Middleware/PwaAuth.php)
- [app/Models/PwaToken.php](file://app/Models/PwaToken.php)
- [app/Jobs/ProcessPwaSyncJob.php](file://app/Jobs/ProcessPwaSyncJob.php)

**Section sources**
- [public/sw.js](file://public/sw.js)
- [routes/api.php](file://routes/api.php)
- [app/Http/Middleware/PwaAuth.php](file://app/Http/Middleware/PwaAuth.php)
- [app/Models/PwaToken.php](file://app/Models/PwaToken.php)
- [app/Jobs/ProcessPwaSyncJob.php](file://app/Jobs/ProcessPwaSyncJob.php)

### Authentication Routes
Key route groups and endpoints:
- Web routes: login, logout, registration, password reset, email verification
- API routes: Sanctum endpoints, PWA token management
- Protected routes: enforced by EnsureRole middleware

```mermaid
flowchart TD
A["routes/web.php"] --> B["GET /login -> LoginForm"]
A --> C["POST /login -> Authenticate"]
A --> D["GET /logout -> Invalidate session"]
A --> E["GET /dashboard -> Protected"]
F["routes/auth.php"] --> G["Auth routes (web)"]
F --> H["Protected by EnsureRole"]
I["routes/api.php"] --> J["Sanctum endpoints"]
I --> K["PWA token endpoints"]
```

**Diagram sources**
- [routes/web.php](file://routes/web.php)
- [routes/auth.php](file://routes/auth.php)
- [routes/api.php](file://routes/api.php)
- [app/Http/Middleware/EnsureRole.php](file://app/Http/Middleware/EnsureRole.php)

**Section sources**
- [routes/web.php](file://routes/web.php)
- [routes/auth.php](file://routes/auth.php)
- [routes/api.php](file://routes/api.php)

### Middleware Application and Route Protection
- PwaAuth: Validates PWA tokens for API requests
- EnsureRole: Restricts access based on user roles
- SessionTimeout: Manages session lifecycle and idle timeouts
- Session-based auth: Uses session store for web flows

```mermaid
flowchart TD
Start(["Incoming Request"]) --> CheckType{"Is PWA/API?"}
CheckType --> |Yes| PwaMW["PwaAuth Middleware"]
CheckType --> |No| WebMW["Web Session Middleware"]
PwaMW --> ValidateToken["Validate PWA token"]
ValidateToken --> TokenValid{"Valid?"}
TokenValid --> |Yes| NextMW["Next Middleware"]
TokenValid --> |No| Unauthorized["401 Unauthorized"]
WebMW --> SessionCheck["Check session validity"]
SessionCheck --> SessionValid{"Valid?"}
SessionValid --> |Yes| NextMW
SessionValid --> |No| RedirectLogin["Redirect to login"]
NextMW --> RoleCheck["EnsureRole Middleware"]
RoleCheck --> Allowed{"Allowed?"}
Allowed --> |Yes| Proceed["Proceed to controller"]
Allowed --> |No| Forbidden["403 Forbidden"]
```

**Diagram sources**
- [app/Http/Middleware/PwaAuth.php](file://app/Http/Middleware/PwaAuth.php)
- [app/Http/Middleware/EnsureRole.php](file://app/Http/Middleware/EnsureRole.php)
- [app/Http/Middleware/SessionTimeout.php](file://app/Http/Middleware/SessionTimeout.php)

**Section sources**
- [app/Http/Middleware/PwaAuth.php](file://app/Http/Middleware/PwaAuth.php)
- [app/Http/Middleware/EnsureRole.php](file://app/Http/Middleware/EnsureRole.php)
- [app/Http/Middleware/SessionTimeout.php](file://app/Http/Middleware/SessionTimeout.php)

### Email Verification and Password Reset
- Email verification: Laravel default verification routes and notifications
- Password reset: Laravel password broker with email notifications
- Configuration: Defined in authentication configuration

```mermaid
sequenceDiagram
participant User as "User"
participant AuthRoutes as "routes/auth.php"
participant Config as "config/auth.php"
participant Mail as "Mail Notification"
participant DB as "Users Table"
User->>AuthRoutes : "Request password reset"
AuthRoutes->>Config : "Use password broker"
Config->>Mail : "Send reset link"
Mail-->>User : "Reset email sent"
User->>AuthRoutes : "Submit new password"
AuthRoutes->>DB : "Update hashed password"
AuthRoutes-->>User : "Success response"
```

**Diagram sources**
- [routes/auth.php](file://routes/auth.php)
- [config/auth.php](file://config/auth.php)

**Section sources**
- [routes/auth.php](file://routes/auth.php)
- [config/auth.php](file://config/auth.php)

### User Feedback and Validation Responses
- Auth session status: Displays success/error messages after auth actions
- Input error components: Render validation errors for form submissions
- Livewire forms: Reactive validation and submission handling

```mermaid
flowchart TD
Submit["Form Submission"] --> Validate["Validate Inputs"]
Validate --> Valid{"Valid?"}
Valid --> |Yes| Success["Show success message"]
Valid --> |No| Errors["Show input errors"]
Success --> StatusComp["auth-session-status component"]
Errors --> ErrorComp["input-error component"]
```

**Diagram sources**
- [resources/views/components/auth-session-status.blade.php](file://resources/views/components/auth-session-status.blade.php)
- [resources/views/components/input-error.blade.php](file://resources/views/components/input-error.blade.php)
- [app/Livewire/Forms/LoginForm.php](file://app/Livewire/Forms/LoginForm.php)

**Section sources**
- [resources/views/components/auth-session-status.blade.php](file://resources/views/components/auth-session-status.blade.php)
- [resources/views/components/input-error.blade.php](file://resources/views/components/input-error.blade.php)
- [app/Livewire/Forms/LoginForm.php](file://app/Livewire/Forms/LoginForm.php)

## Dependency Analysis
Authentication depends on configuration, middleware, models, and frontend components. The following diagram shows key dependencies:

```mermaid
graph TB
AUTH_CONF["config/auth.php"] --> GUARDS["Auth Guards"]
AUTH_CONF --> BROKER["Password Broker"]
SANCTUM["config/sanctum.php"] --> TOKENS["Personal Access Tokens"]
SESSION["config/session.php"] --> SESS_STORE["Session Store"]
PWA_MW["PwaAuth Middleware"] --> PWA_MODEL["PwaToken Model"]
WEB_ROUTES["routes/web.php"] --> LIVEWIRE["Livewire Forms"]
API_ROUTES["routes/api.php"] --> SANCTUM
FEEDBACK["Feedback Components"] --> LIVEWIRE
```

**Diagram sources**
- [config/auth.php](file://config/auth.php)
- [config/sanctum.php](file://config/sanctum.php)
- [config/session.php](file://config/session.php)
- [app/Http/Middleware/PwaAuth.php](file://app/Http/Middleware/PwaAuth.php)
- [app/Models/PwaToken.php](file://app/Models/PwaToken.php)
- [routes/web.php](file://routes/web.php)
- [routes/api.php](file://routes/api.php)
- [resources/views/components/auth-session-status.blade.php](file://resources/views/components/auth-session-status.blade.php)
- [resources/views/components/input-error.blade.php](file://resources/views/components/input-error.blade.php)

**Section sources**
- [config/auth.php](file://config/auth.php)
- [config/sanctum.php](file://config/sanctum.php)
- [config/session.php](file://config/session.php)
- [app/Http/Middleware/PwaAuth.php](file://app/Http/Middleware/PwaAuth.php)
- [app/Models/PwaToken.php](file://app/Models/PwaToken.php)
- [routes/web.php](file://routes/web.php)
- [routes/api.php](file://routes/api.php)

## Performance Considerations
- Prefer Sanctum tokens for API-heavy clients to reduce session overhead
- Use PWA token model judiciously; avoid excessive token churn
- Leverage service worker caching for static assets and app shell
- Minimize middleware stack for public endpoints
- Cache frequently accessed user data with appropriate invalidation

## Troubleshooting Guide
Common issues and resolutions:
- Authentication fails silently: Verify CSRF cookie is present for API requests
- PWA requests unauthorized: Confirm PWA token exists and is valid
- Role-based access denied: Check EnsureRole middleware configuration and user roles
- Session timeout: Review session lifetime and timeout middleware settings
- Email verification failures: Validate mail configuration and notification delivery

**Section sources**
- [config/sanctum.php](file://config/sanctum.php)
- [app/Http/Middleware/PwaAuth.php](file://app/Http/Middleware/PwaAuth.php)
- [app/Http/Middleware/EnsureRole.php](file://app/Http/Middleware/EnsureRole.php)
- [app/Http/Middleware/SessionTimeout.php](file://app/Http/Middleware/SessionTimeout.php)
- [config/session.php](file://config/session.php)

## Conclusion
RaporKM Laravel implements a robust authentication system that supports both traditional web sessions and modern PWA token-based authentication. The architecture cleanly separates concerns across routing, middleware, configuration, and frontend components, enabling secure and scalable authentication flows. Proper middleware application, route protection patterns, and user feedback mechanisms ensure a smooth user experience across web and PWA contexts.