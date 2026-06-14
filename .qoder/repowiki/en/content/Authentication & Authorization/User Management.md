# User Management

<cite>
**Referenced Files in This Document**
- [User.php](file://app/Models/User.php)
- [Ptk.php](file://app/Models/Ptk.php)
- [UserResource.php](file://app/Http/Resources/V1/UserResource.php)
- [PegawaiService.php](file://app/Services/PegawaiService.php)
- [GtkSyncService.php](file://app/Services/Dapodik/GtkSyncService.php)
- [PenggunaSyncService.php](file://app/Services/Dapodik/PenggunaSyncService.php)
- [2026_06_04_120000_create_ptk_table_and_migrate_from_users.php](file://database/migrations/2026_06_04_120000_create_ptk_table_and_migrate_from_users.php)
- [2026_06_01_010827_create_personal_access_tokens_table.php](file://database/migrations/2026_06_01_010827_create_personal_access_tokens_table.php)
- [2026_06_01_010827_create_pwa_tokens_table.php](file://database/migrations/2026_06_01_010827_create_pwa_tokens_table.php)
- [2026_06_10_000001_add_fcm_token_to_users_table.php](file://database/migrations/2026_06_10_000001_add_fcm_token_to_users_table.php)
- [auth.php](file://routes/auth.php)
- [EmailVerificationTest.php](file://tests/Feature/Auth/EmailVerificationTest.php)
- [AuthTest.php](file://tests/Feature/Api/V1/AuthTest.php)
- [LoginForm.php](file://app/Livewire/Forms/LoginForm.php)
- [RememberToken.php](file://app/Models/RememberToken.php)
- [PwaToken.php](file://app/Models/PwaToken.php)
- [PushSubscription.php](file://app/Models/PushSubscription.php)
- [UserPolicy.php](file://app/Policies/UserPolicy.php)
- [UserFactory.php](file://database/factories/UserFactory.php)
- [UserSeeder.php](file://database/seeders/UserSeeder.php)
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
This document describes the user management system in RaporKM Laravel, focusing on the User model, relationships with staff (PTK) entities, role assignments, registration and verification workflows, password management, profile operations, authentication tokens, session handling, and integration with staff records for teachers and administrators. It synthesizes implementation details from migration files, service classes, models, resources, policies, and tests to provide a comprehensive yet accessible guide.

## Project Structure
The user management system spans several layers:
- Models define domain entities and relationships (User, Ptk).
- Services encapsulate business logic for user creation, updates, and synchronization with staff records.
- Migrations establish schema and relationships, including pivot-like integration between users and staff.
- Resources shape serialized API responses.
- Routes and controllers handle authentication flows.
- Tests verify email verification, token handling, and access control.

```mermaid
graph TB
subgraph "Models"
U["User<br/>app/Models/User.php"]
P["Ptk<br/>app/Models/Ptk.php"]
RT["RememberToken<br/>app/Models/RememberToken.php"]
PT["PwaToken<br/>app/Models/PwaToken.php"]
PS["PushSubscription<br/>app/Models/PushSubscription.php"]
end
subgraph "Services"
PG["PegawaiService<br/>app/Services/PegawaiService.php"]
GS["GtkSyncService<br/>app/Services/Dapodik/GtkSyncService.php"]
PU["PenggunaSyncService<br/>app/Services/Dapodik/PenggunaSyncService.php"]
end
subgraph "Resources"
UR["UserResource<br/>app/Http/Resources/V1/UserResource.php"]
end
subgraph "Migrations"
M1["2026_06_04_120000_create_ptk_table_and_migrate_from_users.php"]
M2["2026_06_01_010827_create_personal_access_tokens_table.php"]
M3["2026_06_01_010827_create_pwa_tokens_table.php"]
M4["2026_06_10_000001_add_fcm_token_to_users_table.php"]
end
subgraph "Routing"
R["routes/auth.php"]
end
U --> P
PG --> U
PG --> P
GS --> U
GS --> P
PU --> U
PU --> P
UR --> U
M1 --> U
M1 --> P
M2 --> U
M3 --> U
M4 --> U
R --> U
```

**Diagram sources**
- [User.php](file://app/Models/User.php)
- [Ptk.php](file://app/Models/Ptk.php)
- [PegawaiService.php](file://app/Services/PegawaiService.php)
- [GtkSyncService.php](file://app/Services/Dapodik/GtkSyncService.php)
- [PenggunaSyncService.php](file://app/Services/Dapodik/PenggunaSyncService.php)
- [UserResource.php](file://app/Http/Resources/V1/UserResource.php)
- [2026_06_04_120000_create_ptk_table_and_migrate_from_users.php](file://database/migrations/2026_06_04_120000_create_ptk_table_and_migrate_from_users.php)
- [2026_06_01_010827_create_personal_access_tokens_table.php](file://database/migrations/2026_06_01_010827_create_personal_access_tokens_table.php)
- [2026_06_01_010827_create_pwa_tokens_table.php](file://database/migrations/2026_06_01_010827_create_pwa_tokens_table.php)
- [2026_06_10_000001_add_fcm_token_to_users_table.php](file://database/migrations/2026_06_10_000001_add_fcm_token_to_users_table.php)
- [auth.php](file://routes/auth.php)

**Section sources**
- [User.php](file://app/Models/User.php)
- [PegawaiService.php](file://app/Services/PegawaiService.php)
- [2026_06_04_120000_create_ptk_table_and_migrate_from_users.php](file://database/migrations/2026_06_04_120000_create_ptk_table_and_migrate_from_users.php)

## Core Components
- User model: central identity entity with attributes for personal info, credentials, roles, and staff linkage via ptk_id.
- Ptk model: staff/personnel record linked to User, containing identifiers (NUPTK/NIP/NIK) and demographic data.
- Services:
  - PegawaiService: creates/updates users and synchronizes staff records and menu access for teachers/administrators.
  - GtkSyncService: integrates Dapodik GTK data into users and staff records.
  - PenggunaSyncService: synchronizes general user accounts from external data with deduplication logic.
- Tokens and sessions:
  - Personal access tokens for API authentication.
  - PWA tokens for Progressive Web App sessions.
  - RememberMe tokens and FCM/device metadata stored on users.
- Resources: UserResource serializes user data for API responses.
- Policies: UserPolicy governs authorization rules for user-related actions.

Key implementation highlights:
- Role assignment via jabatan field with constants for teacher, kepala sekolah, and staff.
- Password hashing using framework hashing facilities.
- Staff-user linkage via foreign key ptk_id on users and user_id on ptk.
- Deduplication strategies for user creation/sync based on identifiers and names.

**Section sources**
- [User.php](file://app/Models/User.php)
- [Ptk.php](file://app/Models/Ptk.php)
- [PegawaiService.php](file://app/Services/PegawaiService.php)
- [GtkSyncService.php](file://app/Services/Dapodik/GtkSyncService.php)
- [PenggunaSyncService.php](file://app/Services/Dapodik/PenggunaSyncService.php)
- [UserResource.php](file://app/Http/Resources/V1/UserResource.php)
- [RememberToken.php](file://app/Models/RememberToken.php)
- [PwaToken.php](file://app/Models/PwaToken.php)
- [PushSubscription.php](file://app/Models/PushSubscription.php)
- [UserPolicy.php](file://app/Policies/UserPolicy.php)

## Architecture Overview
The user management architecture separates concerns across models, services, migrations, and resources. Authentication flows leverage Laravel Sanctum for personal access tokens and custom PWA tokens. Staff records are integrated through dedicated services that maintain referential integrity and handle role-specific menu access for educators and administrators.

```mermaid
classDiagram
class User {
+uuid id
+string nama
+string username
+string email
+string password
+int jabatan
+string kontak
+string moto
+string fcm_token
+string device_name
+uuid ptk_id
+isGuru() bool
+isKepsek() bool
+isAdmin() bool
}
class Ptk {
+uuid id
+uuid user_id
+string ptk_id
+string nuptk
+string nik
+string nip
+int kelamin
+string tempat_lahir
+date tanggal_lahir
+int agama
+string pendidikan_terakhir
+string bidang_studi_terakhir
}
class PegawaiService {
+createPegawai(validated) User
+updatePegawai(user, validated) User
+syncPtkRecord(user, validated) void
+syncMenuAkses(user, menuOverrides) void
}
class GtkSyncService {
+sync(item) array
-findExistingPtk(ptk_id, nuptk, nik) Ptk?
-findExistingUser(ptk_id, item) User?
}
class PenggunaSyncService {
+sync(item, role) array
-findExistingUser(ptk_id, email, username, item, role) User?
-updateExistingUser(existingUser, item, username, email, role, ptk_id) void
-createNewUser(item, username, email, role, ptk_id) void
}
User --> Ptk : "belongsTo via ptk_id"
PegawaiService --> User : "creates/updates"
PegawaiService --> Ptk : "syncs/stores"
GtkSyncService --> User : "ensures"
GtkSyncService --> Ptk : "creates/updates"
PenggunaSyncService --> User : "syncs"
PenggunaSyncService --> Ptk : "links"
```

**Diagram sources**
- [User.php](file://app/Models/User.php)
- [Ptk.php](file://app/Models/Ptk.php)
- [PegawaiService.php](file://app/Services/PegawaiService.php)
- [GtkSyncService.php](file://app/Services/Dapodik/GtkSyncService.php)
- [PenggunaSyncService.php](file://app/Services/Dapodik/PenggunaSyncService.php)

## Detailed Component Analysis

### User Model and Relationships
- Attributes include personal info, credentials, contact, role (jabatan), and optional staff linkage (ptk_id).
- Roles:
  - Teacher: isGuru()
  - Headteacher: isKepsek()
  - Administrator/Staff: isAdmin()
- Relationship: User belongs to Ptk via optional ptk_id, enabling staff integration for educators and administrators.

```mermaid
classDiagram
class User {
+uuid id
+string nama
+string username
+string email
+string password
+int jabatan
+string kontak
+string moto
+string fcm_token
+string device_name
+uuid ptk_id
+isGuru() bool
+isKepsek() bool
+isAdmin() bool
}
class Ptk {
+uuid id
+uuid user_id
+string ptk_id
+string nuptk
+string nik
+string nip
+int kelamin
+string tempat_lahir
+date tanggal_lahir
+int agama
+string pendidikan_terakhir
+string bidang_studi_terakhir
}
User --> Ptk : "belongsTo ptk_id"
```

**Diagram sources**
- [User.php](file://app/Models/User.php)
- [Ptk.php](file://app/Models/Ptk.php)

**Section sources**
- [User.php](file://app/Models/User.php)
- [Ptk.php](file://app/Models/Ptk.php)
- [2026_06_04_120000_create_ptk_table_and_migrate_from_users.php](file://database/migrations/2026_06_04_120000_create_ptk_table_and_migrate_from_users.php)

### Staff Record Integration (Ptk)
- Migration establishes foreign key relationship from users.ptk_id to ptk.id and migrates existing GTK data from users to ptk.
- Services create/update Ptk records when user data includes staff identifiers (NIP/NUPTK/NIK).

```mermaid
flowchart TD
Start(["Sync Staff Data"]) --> CheckPtk["Find existing Ptk by ptk_id/nuptk/nik"]
CheckPtk --> ExistsPtk{"Ptk exists?"}
ExistsPtk --> |Yes| UpdatePtk["Update Ptk fields"]
ExistsPtk --> |No| CreatePtk["Create Ptk with user_id"]
UpdatePtk --> LinkUser["Link user.ptk_id if missing"]
CreatePtk --> LinkUser
LinkUser --> End(["Done"])
```

**Diagram sources**
- [2026_06_04_120000_create_ptk_table_and_migrate_from_users.php](file://database/migrations/2026_06_04_120000_create_ptk_table_and_migrate_from_users.php)
- [GtkSyncService.php](file://app/Services/Dapodik/GtkSyncService.php)

**Section sources**
- [2026_06_04_120000_create_ptk_table_and_migrate_from_users.php](file://database/migrations/2026_06_04_120000_create_ptk_table_and_migrate_from_users.php)
- [GtkSyncService.php](file://app/Services/Dapodik/GtkSyncService.php)

### User Creation, Modification, and Deletion
- Creation:
  - Via PegawaiService for internal management, hashing passwords and optionally linking staff records.
  - Via GtkSyncService for Dapodik GTK integration, generating usernames and default passwords.
  - Via PenggunaSyncService for general user synchronization with deduplication.
- Modification:
  - Update user profile fields and password when provided.
  - Sync staff record and menu access for teachers/administrators.
- Deletion:
  - Not explicitly shown in referenced files; typical Laravel soft/hard delete patterns apply depending on policy.

```mermaid
sequenceDiagram
participant Admin as "Admin/TU"
participant Service as "PegawaiService"
participant User as "User"
participant Ptk as "Ptk"
Admin->>Service : createPegawai(validated)
Service->>User : create(userData with hashed password)
Service->>Ptk : create Ptk if identifiers present
Service->>User : set ptk_id
Service-->>Admin : return User
```

**Diagram sources**
- [PegawaiService.php](file://app/Services/PegawaiService.php)
- [User.php](file://app/Models/User.php)
- [Ptk.php](file://app/Models/Ptk.php)

**Section sources**
- [PegawaiService.php](file://app/Services/PegawaiService.php)
- [GtkSyncService.php](file://app/Services/Dapodik/GtkSyncService.php)
- [PenggunaSyncService.php](file://app/Services/Dapodik/PenggunaSyncService.php)

### Registration and Email Verification
- Email verification screen rendering and signed route verification are covered by tests.
- Verification flow ensures email is marked verified and redirects to dashboard with success indicator.

```mermaid
sequenceDiagram
participant User as "Unverified User"
participant Route as "routes/auth.php"
participant Test as "EmailVerificationTest"
User->>Route : GET /verify-email
Route-->>User : Render verification page
User->>Route : GET signed verification link
Route-->>User : Mark email verified and redirect to dashboard
Test-->>Test : Assert event dispatched and verified flag true
```

**Diagram sources**
- [EmailVerificationTest.php](file://tests/Feature/Auth/EmailVerificationTest.php)
- [auth.php](file://routes/auth.php)

**Section sources**
- [EmailVerificationTest.php](file://tests/Feature/Auth/EmailVerificationTest.php)
- [auth.php](file://routes/auth.php)

### Password Management
- Hashing: Passwords are hashed during user creation and updates using framework hashing facilities.
- Reset mechanisms: Not visible in referenced files; typically handled by Laravel password reset functionality and Sanctum token lifecycle.

```mermaid
flowchart TD
Start(["Set/Change Password"]) --> Hash["Hash new password"]
Hash --> Save["Persist to User.password"]
Save --> End(["Done"])
```

**Diagram sources**
- [PegawaiService.php](file://app/Services/PegawaiService.php)

**Section sources**
- [PegawaiService.php](file://app/Services/PegawaiService.php)

### Authentication Tokens and Sessions
- Personal Access Tokens: Created per device for API access; login with different devices maintains separate tokens.
- PWA Tokens: Dedicated tokens for Progressive Web App sessions with expiration.
- Remember Me: Stored remember tokens for persistent sessions.
- Device Metadata: Users store optional device_name and FCM token for notifications.

```mermaid
sequenceDiagram
participant Client as "Client"
participant User as "User"
participant PAT as "Personal Access Token"
participant PWAT as "PWA Token"
Client->>User : Login with device_name
User->>PAT : createToken(device_name)
User->>PWAT : create PWA token if applicable
Client-->>User : Use tokens for protected API calls
```

**Diagram sources**
- [2026_06_01_010827_create_personal_access_tokens_table.php](file://database/migrations/2026_06_01_010827_create_personal_access_tokens_table.php)
- [2026_06_01_010827_create_pwa_tokens_table.php](file://database/migrations/2026_06_01_010827_create_pwa_tokens_table.php)
- [2026_06_10_000001_add_fcm_token_to_users_table.php](file://database/migrations/2026_06_10_000001_add_fcm_token_to_users_table.php)
- [AuthTest.php](file://tests/Feature/Api/V1/AuthTest.php)

**Section sources**
- [2026_06_01_010827_create_personal_access_tokens_table.php](file://database/migrations/2026_06_01_010827_create_personal_access_tokens_table.php)
- [2026_06_01_010827_create_pwa_tokens_table.php](file://database/migrations/2026_06_01_010827_create_pwa_tokens_table.php)
- [2026_06_10_000001_add_fcm_token_to_users_table.php](file://database/migrations/2026_06_10_000001_add_fcm_token_to_users_table.php)
- [AuthTest.php](file://tests/Feature/Api/V1/AuthTest.php)

### Profile Management
- UserResource serializes user data for API responses, ensuring consistent exposure of profile fields.
- Profile updates are handled via service methods that update user and associated staff records.

**Section sources**
- [UserResource.php](file://app/Http/Resources/V1/UserResource.php)
- [PegawaiService.php](file://app/Services/PegawaiService.php)

### Authorization and Access Control
- UserPolicy defines authorization rules for user-related actions.
- Menu access overrides for teachers/headteachers are synchronized via service logic.

**Section sources**
- [UserPolicy.php](file://app/Policies/UserPolicy.php)
- [PegawaiService.php](file://app/Services/PegawaiService.php)

## Dependency Analysis
The user management system exhibits clear separation of concerns:
- Models depend on Eloquent ORM and relationships.
- Services encapsulate business logic and coordinate model interactions.
- Migrations define schema and relationships, including foreign keys and indexes.
- Resources transform models for API consumption.
- Tests validate authentication flows and token behavior.

```mermaid
graph LR
U["User"] --> P["Ptk"]
PG["PegawaiService"] --> U
PG --> P
GS["GtkSyncService"] --> U
GS --> P
PU["PenggunaSyncService"] --> U
PU --> P
UR["UserResource"] --> U
M1["Create PTK migration"] --> U
M1 --> P
M2["Personal Access Tokens"] --> U
M3["PWA Tokens"] --> U
M4["FCM/Device fields"] --> U
```

**Diagram sources**
- [User.php](file://app/Models/User.php)
- [Ptk.php](file://app/Models/Ptk.php)
- [PegawaiService.php](file://app/Services/PegawaiService.php)
- [GtkSyncService.php](file://app/Services/Dapodik/GtkSyncService.php)
- [PenggunaSyncService.php](file://app/Services/Dapodik/PenggunaSyncService.php)
- [UserResource.php](file://app/Http/Resources/V1/UserResource.php)
- [2026_06_04_120000_create_ptk_table_and_migrate_from_users.php](file://database/migrations/2026_06_04_120000_create_ptk_table_and_migrate_from_users.php)
- [2026_06_01_010827_create_personal_access_tokens_table.php](file://database/migrations/2026_06_01_010827_create_personal_access_tokens_table.php)
- [2026_06_01_010827_create_pwa_tokens_table.php](file://database/migrations/2026_06_01_010827_create_pwa_tokens_table.php)
- [2026_06_10_000001_add_fcm_token_to_users_table.php](file://database/migrations/2026_06_10_000001_add_fcm_token_to_users_table.php)

**Section sources**
- [User.php](file://app/Models/User.php)
- [PegawaiService.php](file://app/Services/PegawaiService.php)
- [2026_06_04_120000_create_ptk_table_and_migrate_from_users.php](file://database/migrations/2026_06_04_120000_create_ptk_table_and_migrate_from_users.php)

## Performance Considerations
- Indexing: Foreign keys and unique constraints on tokens and identifiers improve lookup performance.
- Token lifecycle: Personal access tokens enable per-device sessions; manage token rotation to limit long-lived credentials.
- Deduplication: Synchronization services avoid redundant user creation by checking multiple identifiers.
- Serialization: Resource classes ensure efficient API responses by selecting necessary fields.

## Troubleshooting Guide
- Email verification failures:
  - Verify signed route generation and hash matching.
  - Confirm user is unverified before attempting verification.
- Token issues:
  - Ensure device_name uniqueness per user for personal access tokens.
  - Validate PWA token expiration and existence.
- Staff linkage problems:
  - Confirm ptk_id is set after creating staff records.
  - Check migration completeness for foreign key constraints.

**Section sources**
- [EmailVerificationTest.php](file://tests/Feature/Auth/EmailVerificationTest.php)
- [AuthTest.php](file://tests/Feature/Api/V1/AuthTest.php)
- [2026_06_01_010827_create_pwa_tokens_table.php](file://database/migrations/2026_06_01_010827_create_pwa_tokens_table.php)

## Conclusion
RaporKM’s user management system integrates users with staff records through a robust model-service architecture. It supports role-based access, secure password handling, flexible authentication tokens, and comprehensive synchronization from external data sources. The modular design enables maintainable extensions for profile management, authorization, and integration workflows.