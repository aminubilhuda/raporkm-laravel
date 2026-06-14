# Extracurricular Activities

<cite>
**Referenced Files in This Document**
- [Eskul.php](file://app/Models/Eskul.php)
- [Organisasi.php](file://app/Models/Organisasi.php)
- [SiswaEskul.php](file://app/Models/SiswaEskul.php)
- [PembinaEskul.php](file://app/Models/PembinaEskul.php)
- [DeskripsiKokurikuler.php](file://app/Models/DeskripsiKokurikuler.php)
- [Prestasi.php](file://app/Models/Prestasi.php)
- [2026_06_01_010809_create_eskul_table.php](file://database/migrations/2026_06_01_010809_create_eskul_table.php)
- [2026_06_01_010809_create_organisasi_table.php](file://database/migrations/2026_06_01_010809_create_organisasi_table.php)
- [2026_06_01_010816_create_pembina_eskul_table.php](file://database/migrations/2026_06_01_010816_create_pembina_eskul_table.php)
- [2026_06_01_010820_create_siswa_eskul_table.php](file://database/migrations/2026_06_01_010820_create_siswa_eskul_table.php)
- [2026_06_01_010809_create_deskripsi_kokurikuler_table.php](file://database/migrations/2026_06_01_010809_create_deskripsi_kokurikuler_table.php)
- [2026_06_01_010821_create_prestasi_table.php](file://database/migrations/2026_06_01_010821_create_prestasi_table.php)
- [index.blade.php (TU Organisasi)](file://resources/views/tu/organisasi/index.blade.php)
- [index.blade.php (Guru Organisasi)](file://resources/views/guru/organisasi/index.blade.php)
- [index.blade.php (TU Prestasi)](file://resources/views/tu/prestasi/index.blade.php)
- [06-ekstra-organisasi.md](file://docs/manual-tu/06-ekstra-organisasi.md)
- [09-piket-organisasi.md](file://docs/manual-guru/09-piket-organisasi.md)
- [NilaiKokurikuler.php](file://app/Models/NilaiKokurikuler.php)
- [Siswa.php](file://app/Models/Siswa.php)
- [web.php](file://routes/web.php)
- [GuruMenuService.php](file://app/Services/GuruMenuService.php)
- [ExportService.php](file://app/Services/ExportService.php)
- [RaporService.php](file://app/Services/RaporService.php)
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
10. [Appendices](#appendices)

## Introduction
This document describes the extracurricular activities management capabilities implemented in the system, focusing on student organization participation tracking, club membership management, and sports program coordination. It explains how activities are set up, how students register, and how activity schedules are maintained. It also covers the relationship between academic subjects and extracurricular involvement, including time management and academic impact considerations, and documents evaluation systems, participation certificates, and achievement recognition. Finally, it outlines integrations with student profiles, activity reports, and school-wide dashboards, with practical examples, best practices, and troubleshooting guidance.

## Project Structure
The extracurricular domain spans database models, migrations, views, documentation, and services:
- Models define entities such as extracurricular clubs, organizations, student memberships, advisors, descriptors, and achievements.
- Migrations establish the relational schema for activity setup and scheduling.
- Views provide teacher and TU dashboards for managing organizations and recognizing achievements.
- Documentation offers procedural guidance for activity setup and organization management.
- Services support reporting and export functionalities.

```mermaid
graph TB
subgraph "Models"
E["Eskul"]
O["Organisasi"]
SE["SiswaEskul"]
PE["PembinaEskul"]
DK["DeskripsiKokurikuler"]
PR["Prestasi"]
NI["NilaiKokurikuler"]
SS["Siswa"]
end
subgraph "Migrations"
ME["create_eskul_table"]
MO["create_organisasi_table"]
MSE["create_siswa_eskul_table"]
MPE["create_pembina_eskul_table"]
MDK["create_deskripsi_kokurikuler_table"]
MPR["create_prestasi_table"]
end
subgraph "Views"
VO_TU["TU Organisasi Index"]
VO_GURU["Guru Organisasi Index"]
VP_TU["TU Prestasi Index"]
end
subgraph "Docs"
DOC_TU["Manual TU: Ekstra & Organisasi"]
DOC_GURU["Manual Guru: Piket & Organisasi"]
end
E --- ME
O --- MO
SE --- MSE
PE --- MPE
DK --- MDK
PR --- MPR
SE --- SS
E --- SE
O --- SE
PE --- E
DK --- NI
PR --- SS
VO_TU --- O
VO_GURU --- O
VP_TU --- PR
```

**Diagram sources**
- [Eskul.php](file://app/Models/Eskul.php)
- [Organisasi.php](file://app/Models/Organisasi.php)
- [SiswaEskul.php](file://app/Models/SiswaEskul.php)
- [PembinaEskul.php](file://app/Models/PembinaEskul.php)
- [DeskripsiKokurikuler.php](file://app/Models/DeskripsiKokurikuler.php)
- [Prestasi.php](file://app/Models/Prestasi.php)
- [2026_06_01_010809_create_eskul_table.php](file://database/migrations/2026_06_01_010809_create_eskul_table.php)
- [2026_06_01_010809_create_organisasi_table.php](file://database/migrations/2026_06_01_010809_create_organisasi_table.php)
- [2026_06_01_010816_create_pembina_eskul_table.php](file://database/migrations/2026_06_01_010816_create_pembina_eskul_table.php)
- [2026_06_01_010820_create_siswa_eskul_table.php](file://database/migrations/2026_06_01_010820_create_siswa_eskul_table.php)
- [2026_06_01_010809_create_deskripsi_kokurikuler_table.php](file://database/migrations/2026_06_01_010809_create_deskripsi_kokurikuler_table.php)
- [2026_06_01_010821_create_prestasi_table.php](file://database/migrations/2026_06_01_010821_create_prestasi_table.php)
- [index.blade.php (TU Organisasi)](file://resources/views/tu/organisasi/index.blade.php)
- [index.blade.php (Guru Organisasi)](file://resources/views/guru/organisasi/index.blade.php)
- [index.blade.php (TU Prestasi)](file://resources/views/tu/prestasi/index.blade.php)
- [06-ekstra-organisasi.md](file://docs/manual-tu/06-ekstra-organisasi.md)
- [09-piket-organisasi.md](file://docs/manual-guru/09-piket-organisasi.md)

**Section sources**
- [Eskul.php](file://app/Models/Eskul.php)
- [Organisasi.php](file://app/Models/Organisasi.php)
- [SiswaEskul.php](file://app/Models/SiswaEskul.php)
- [PembinaEskul.php](file://app/Models/PembinaEskul.php)
- [DeskripsiKokurikuler.php](file://app/Models/DeskripsiKokurikuler.php)
- [Prestasi.php](file://app/Models/Prestasi.php)
- [2026_06_01_010809_create_eskul_table.php](file://database/migrations/2026_06_01_010809_create_eskul_table.php)
- [2026_06_01_010809_create_organisasi_table.php](file://database/migrations/2026_06_01_010809_create_organisasi_table.php)
- [2026_06_01_010816_create_pembina_eskul_table.php](file://database/migrations/2026_06_01_010816_create_pembina_eskul_table.php)
- [2026_06_01_010820_create_siswa_eskul_table.php](file://database/migrations/2026_06_01_010820_create_siswa_eskul_table.php)
- [2026_06_01_010809_create_deskripsi_kokurikuler_table.php](file://database/migrations/2026_06_01_010809_create_deskripsi_kokurikuler_table.php)
- [2026_06_01_010821_create_prestasi_table.php](file://database/migrations/2026_06_01_010821_create_prestasi_table.php)
- [index.blade.php (TU Organisasi)](file://resources/views/tu/organisasi/index.blade.php)
- [index.blade.php (Guru Organisasi)](file://resources/views/guru/organisasi/index.blade.php)
- [index.blade.php (TU Prestasi)](file://resources/views/tu/prestasi/index.blade.php)
- [06-ekstra-organisasi.md](file://docs/manual-tu/06-ekstra-organisasi.md)
- [09-piket-organisasi.md](file://docs/manual-guru/09-piket-organisasi.md)

## Core Components
- Activity catalog and scheduling: managed via the extracurricular club entity and associated advisor records.
- Student participation tracking: recorded through membership junction entries linking students to activities.
- Organization oversight: managed via organizational units that can host multiple activities.
- Academic descriptors and evaluations: linked to co-curricular descriptors for grading and reporting.
- Achievement recognition: captured through achievement records tied to students.
- Dashboards and reporting: surfaced in TU and Guru views for oversight and export.

Key implementation anchors:
- Activity setup and scheduling: [Eskul.php](file://app/Models/Eskul.php), [2026_06_01_010809_create_eskul_table.php](file://database/migrations/2026_06_01_010809_create_eskul_table.php)
- Student membership: [SiswaEskul.php](file://app/Models/SiswaEskul.php), [2026_06_01_010820_create_siswa_eskul_table.php](file://database/migrations/2026_06_01_010820_create_siswa_eskul_table.php)
- Organization hosting: [Organisasi.php](file://app/Models/Organisasi.php), [2026_06_01_010809_create_organisasi_table.php](file://database/migrations/2026_06_01_010809_create_organisasi_table.php)
- Advisor assignment: [PembinaEskul.php](file://app/Models/PembinaEskul.php), [2026_06_01_010816_create_pembina_eskul_table.php](file://database/migrations/2026_06_01_010816_create_pembina_eskul_table.php)
- Descriptors and evaluations: [DeskripsiKokurikuler.php](file://app/Models/DeskripsiKokurikuler.php), [NilaiKokurikuler.php](file://app/Models/NilaiKokurikuler.php)
- Achievements: [Prestasi.php](file://app/Models/Prestasi.php), [2026_06_01_010821_create_prestasi_table.php](file://database/migrations/2026_06_01_010821_create_prestasi_table.php)
- Dashboards and views: [index.blade.php (TU Organisasi)](file://resources/views/tu/organisasi/index.blade.php), [index.blade.php (Guru Organisasi)](file://resources/views/guru/organisasi/index.blade.php), [index.blade.php (TU Prestasi)](file://resources/views/tu/prestasi/index.blade.php)
- Procedures and guidance: [06-ekstra-organisasi.md](file://docs/manual-tu/06-ekstra-organisasi.md), [09-piket-organisasi.md](file://docs/manual-guru/09-piket-organisasi.md)

**Section sources**
- [Eskul.php](file://app/Models/Eskul.php)
- [SiswaEskul.php](file://app/Models/SiswaEskul.php)
- [Organisasi.php](file://app/Models/Organisasi.php)
- [PembinaEskul.php](file://app/Models/PembinaEskul.php)
- [DeskripsiKokurikuler.php](file://app/Models/DeskripsiKokurikuler.php)
- [NilaiKokurikuler.php](file://app/Models/NilaiKokurikuler.php)
- [Prestasi.php](file://app/Models/Prestasi.php)
- [2026_06_01_010809_create_eskul_table.php](file://database/migrations/2026_06_01_010809_create_eskul_table.php)
- [2026_06_01_010820_create_siswa_eskul_table.php](file://database/migrations/2026_06_01_010820_create_siswa_eskul_table.php)
- [2026_06_01_010809_create_organisasi_table.php](file://database/migrations/2026_06_01_010809_create_organisasi_table.php)
- [2026_06_01_010816_create_pembina_eskul_table.php](file://database/migrations/2026_06_01_010816_create_pembina_eskul_table.php)
- [2026_06_01_010809_create_deskripsi_kokurikuler_table.php](file://database/migrations/2026_06_01_010809_create_deskripsi_kokurikuler_table.php)
- [2026_06_01_010821_create_prestasi_table.php](file://database/migrations/2026_06_01_010821_create_prestasi_table.php)
- [index.blade.php (TU Organisasi)](file://resources/views/tu/organisasi/index.blade.php)
- [index.blade.php (Guru Organisasi)](file://resources/views/guru/organisasi/index.blade.php)
- [index.blade.php (TU Prestasi)](file://resources/views/tu/prestasi/index.blade.php)
- [06-ekstra-organisasi.md](file://docs/manual-tu/06-ekstra-organisasi.md)
- [09-piket-organisasi.md](file://docs/manual-guru/09-piket-organisasi.md)

## Architecture Overview
The extracurricular subsystem integrates data modeling, UI dashboards, and administrative procedures:
- Data model: clubs, organizations, memberships, advisors, descriptors, and achievements form a cohesive schema.
- UI: TU and Guru views present organization management and achievement recognition interfaces.
- Procedures: documented workflows guide setup, scheduling, and oversight.

```mermaid
graph TB
UI_TU_ORG["TU Organisasi View"]
UI_GURU_ORG["Guru Organisasi View"]
UI_TU_PRESTASI["TU Prestasi View"]
CTRL_TU["TU Controllers"]
CTRL_GURU["Guru Controllers"]
MODEL_ESKUL["Eskul Model"]
MODEL_ORG["Organisasi Model"]
MODEL_SE["SiswaEskul Model"]
MODEL_PE["PembinaEskul Model"]
MODEL_DK["DeskripsiKokurikuler Model"]
MODEL_PR["Prestasi Model"]
MODEL_SS["Siswa Model"]
DB["Database Schema"]
UI_TU_ORG --> CTRL_TU
UI_GURU_ORG --> CTRL_GURU
UI_TU_PRESTASI --> CTRL_TU
CTRL_TU --> MODEL_ESKUL
CTRL_TU --> MODEL_ORG
CTRL_TU --> MODEL_SE
CTRL_TU --> MODEL_PE
CTRL_TU --> MODEL_PR
CTRL_GURU --> MODEL_ESKUL
CTRL_GURU --> MODEL_ORG
CTRL_GURU --> MODEL_SE
CTRL_GURU --> MODEL_PE
CTRL_GURU --> MODEL_DK
MODEL_ESKUL --> DB
MODEL_ORG --> DB
MODEL_SE --> DB
MODEL_PE --> DB
MODEL_DK --> DB
MODEL_PR --> DB
MODEL_SS --> DB
```

**Diagram sources**
- [index.blade.php (TU Organisasi)](file://resources/views/tu/organisasi/index.blade.php)
- [index.blade.php (Guru Organisasi)](file://resources/views/guru/organisasi/index.blade.php)
- [index.blade.php (TU Prestasi)](file://resources/views/tu/prestasi/index.blade.php)
- [Eskul.php](file://app/Models/Eskul.php)
- [Organisasi.php](file://app/Models/Organisasi.php)
- [SiswaEskul.php](file://app/Models/SiswaEskul.php)
- [PembinaEskul.php](file://app/Models/PembinaEskul.php)
- [DeskripsiKokurikuler.php](file://app/Models/DeskripsiKokurikuler.php)
- [Prestasi.php](file://app/Models/Prestasi.php)
- [Siswa.php](file://app/Models/Siswa.php)

## Detailed Component Analysis

### Activity Catalog and Scheduling (Clubs)
- Purpose: Define and schedule extracurricular clubs.
- Entities:
  - Club: [Eskul.php](file://app/Models/Eskul.php)
  - Organization: [Organisasi.php](file://app/Models/Organisasi.php)
  - Advisor: [PembinaEskul.php](file://app/Models/PembinaEskul.php)
- Schema:
  - Clubs: [2026_06_01_010809_create_eskul_table.php](file://database/migrations/2026_06_01_010809_create_eskul_table.php)
  - Organizations: [2026_06_01_010809_create_organisasi_table.php](file://database/migrations/2026_06_01_010809_create_organisasi_table.php)
  - Advisors: [2026_06_01_010816_create_pembina_eskul_table.php](file://database/migrations/2026_06_01_010816_create_pembina_eskul_table.php)

```mermaid
classDiagram
class Eskul {
+uuid id
+string nama
+text deskripsi
+uuid organisasi_id
+datetime created_at
+datetime updated_at
}
class Organisasi {
+uuid id
+string nama
+text deskripsi
+datetime created_at
+datetime updated_at
}
class PembinaEskul {
+uuid id
+uuid eskul_id
+uuid guru_id
+datetime mulai
+datetime selesai
+datetime created_at
+datetime updated_at
}
Eskul --> Organisasi : "hosted_by"
PembinaEskul --> Eskul : "advises"
```

**Diagram sources**
- [Eskul.php](file://app/Models/Eskul.php)
- [Organisasi.php](file://app/Models/Organisasi.php)
- [PembinaEskul.php](file://app/Models/PembinaEskul.php)
- [2026_06_01_010809_create_eskul_table.php](file://database/migrations/2026_06_01_010809_create_eskul_table.php)
- [2026_06_01_010809_create_organisasi_table.php](file://database/migrations/2026_06_01_010809_create_organisasi_table.php)
- [2026_06_01_010816_create_pembina_eskul_table.php](file://database/migrations/2026_06_01_010816_create_pembina_eskul_table.php)

**Section sources**
- [Eskul.php](file://app/Models/Eskul.php)
- [Organisasi.php](file://app/Models/Organisasi.php)
- [PembinaEskul.php](file://app/Models/PembinaEskul.php)
- [2026_06_01_010809_create_eskul_table.php](file://database/migrations/2026_06_01_010809_create_eskul_table.php)
- [2026_06_01_010809_create_organisasi_table.php](file://database/migrations/2026_06_01_010809_create_organisasi_table.php)
- [2026_06_01_010816_create_pembina_eskul_table.php](file://database/migrations/2026_06_01_010816_create_pembina_eskul_table.php)

### Student Participation Tracking (Membership)
- Purpose: Track which students participate in which activities.
- Entities:
  - Membership: [SiswaEskul.php](file://app/Models/SiswaEskul.php)
  - Student: [Siswa.php](file://app/Models/Siswa.php)
- Schema:
  - Membership: [2026_06_01_010820_create_siswa_eskul_table.php](file://database/migrations/2026_06_01_010820_create_siswa_eskul_table.php)

```mermaid
classDiagram
class Siswa {
+uuid id
+string nama
+uuid nisn
+datetime created_at
+datetime updated_at
}
class Eskul {
+uuid id
+string nama
+uuid organisasi_id
+datetime created_at
+datetime updated_at
}
class SiswaEskul {
+uuid id
+uuid siswa_id
+uuid eskul_id
+date tanggal_daftar
+enum status
+datetime created_at
+datetime updated_at
}
SiswaEskul --> Siswa : "student"
SiswaEskul --> Eskul : "activity"
```

**Diagram sources**
- [SiswaEskul.php](file://app/Models/SiswaEskul.php)
- [Siswa.php](file://app/Models/Siswa.php)
- [Eskul.php](file://app/Models/Eskul.php)
- [2026_06_01_010820_create_siswa_eskul_table.php](file://database/migrations/2026_06_01_010820_create_siswa_eskul_table.php)

**Section sources**
- [SiswaEskul.php](file://app/Models/SiswaEskul.php)
- [Siswa.php](file://app/Models/Siswa.php)
- [Eskul.php](file://app/Models/Eskul.php)
- [2026_06_01_010820_create_siswa_eskul_table.php](file://database/migrations/2026_06_01_010820_create_siswa_eskul_table.php)

### Organization Hosting and Oversight
- Purpose: Manage organizational units that host activities and coordinate schedules.
- Entities:
  - Organization: [Organisasi.php](file://app/Models/Organisasi.php)
  - Views: [index.blade.php (TU Organisasi)](file://resources/views/tu/organisasi/index.blade.php), [index.blade.php (Guru Organisasi)](file://resources/views/guru/organisasi/index.blade.php)
- Schema:
  - Organizations: [2026_06_01_010809_create_organisasi_table.php](file://database/migrations/2026_06_01_010809_create_organisasi_table.php)

```mermaid
flowchart TD
Start(["Organization Management"]) --> CreateOrg["Create Organization"]
CreateOrg --> AssignActivity["Assign Activities to Organization"]
AssignActivity --> Schedule["Schedule Activity Sessions"]
Schedule --> Monitor["Monitor Participation"]
Monitor --> Evaluate["Evaluate Participation"]
Evaluate --> Report["Generate Reports"]
Report --> End(["Done"])
```

**Diagram sources**
- [Organisasi.php](file://app/Models/Organisasi.php)
- [2026_06_01_010809_create_organisasi_table.php](file://database/migrations/2026_06_01_010809_create_organisasi_table.php)
- [index.blade.php (TU Organisasi)](file://resources/views/tu/organisasi/index.blade.php)
- [index.blade.php (Guru Organisasi)](file://resources/views/guru/organisasi/index.blade.php)

**Section sources**
- [Organisasi.php](file://app/Models/Organisasi.php)
- [2026_06_01_010809_create_organisasi_table.php](file://database/migrations/2026_06_01_010809_create_organisasi_table.php)
- [index.blade.php (TU Organisasi)](file://resources/views/tu/organisasi/index.blade.php)
- [index.blade.php (Guru Organisasi)](file://resources/views/guru/organisasi/index.blade.php)

### Academic Descriptors and Evaluation
- Purpose: Link co-curricular descriptors to academic evaluation and reporting.
- Entities:
  - Descriptor: [DeskripsiKokurikuler.php](file://app/Models/DeskripsiKokurikuler.php)
  - Evaluation: [NilaiKokurikuler.php](file://app/Models/NilaiKokurikuler.php)
- Schema:
  - Descriptors: [2026_06_01_010809_create_deskripsi_kokurikuler_table.php](file://database/migrations/2026_06_01_010809_create_deskripsi_kokurikuler_table.php)

```mermaid
classDiagram
class DeskripsiKokurikuler {
+uuid id
+string dimensi
+string elemen
+text deskripsi
+datetime created_at
+datetime updated_at
}
class NilaiKokurikuler {
+uuid id
+uuid siswa_id
+uuid deskripsi_id
+string nilai
+text predikat
+text temuan
+datetime tahun_ajaran
+datetime created_at
+datetime updated_at
}
NilaiKokurikuler --> DeskripsiKokurikuler : "evaluates"
```

**Diagram sources**
- [DeskripsiKokurikuler.php](file://app/Models/DeskripsiKokurikuler.php)
- [NilaiKokurikuler.php](file://app/Models/NilaiKokurikuler.php)
- [2026_06_01_010809_create_deskripsi_kokurikuler_table.php](file://database/migrations/2026_06_01_010809_create_deskripsi_kokurikuler_table.php)

**Section sources**
- [DeskripsiKokurikuler.php](file://app/Models/DeskripsiKokurikuler.php)
- [NilaiKokurikuler.php](file://app/Models/NilaiKokurikuler.php)
- [2026_06_01_010809_create_deskripsi_kokurikuler_table.php](file://database/migrations/2026_06_01_010809_create_deskripsi_kokurikuler_table.php)

### Achievement Recognition and Certificates
- Purpose: Recognize student achievements and manage certificates.
- Entities:
  - Achievement: [Prestasi.php](file://app/Models/Prestasi.php)
  - Views: [index.blade.php (TU Prestasi)](file://resources/views/tu/prestasi/index.blade.php)
- Schema:
  - Achievements: [2026_06_01_010821_create_prestasi_table.php](file://database/migrations/2026_06_01_010821_create_prestasi_table.php)

```mermaid
sequenceDiagram
participant TU as "TU User"
participant View as "Prestasi View"
participant Model as "Prestasi Model"
participant DB as "Database"
TU->>View : Open Achievement Management
View->>Model : Load achievements for selected student
Model->>DB : Query by student and criteria
DB-->>Model : Records
Model-->>View : Results
View-->>TU : Display achievements and actions
```

**Diagram sources**
- [index.blade.php (TU Prestasi)](file://resources/views/tu/prestasi/index.blade.php)
- [Prestasi.php](file://app/Models/Prestasi.php)
- [2026_06_01_010821_create_prestasi_table.php](file://database/migrations/2026_06_01_010821_create_prestasi_table.php)

**Section sources**
- [Prestasi.php](file://app/Models/Prestasi.php)
- [index.blade.php (TU Prestasi)](file://resources/views/tu/prestasi/index.blade.php)
- [2026_06_01_010821_create_prestasi_table.php](file://database/migrations/2026_06_01_010821_create_prestasi_table.php)

### Relationship Between Academic Subjects and Extracurricular Involvement
- Purpose: Understand how academic subjects relate to co-curricular participation and how time allocation affects academic performance.
- Entities:
  - Subject enrollment: [MapelSiswa.php](file://app/Models/MapelSiswa.php)
  - Student profile: [Siswa.php](file://app/Models/Siswa.php)
  - Co-curricular evaluation: [NilaiKokurikuler.php](file://app/Models/NilaiKokurikuler.php)
- Guidance:
  - Time management and academic impact considerations are covered in the documentation for teachers and TU staff.

```mermaid
flowchart TD
A["Student Enrollment in Subjects"] --> B["Co-curricular Participation"]
B --> C["Time Allocation"]
C --> D["Academic Performance Impact"]
D --> E["Descriptor Evaluation"]
E --> F["Report Generation"]
```

[No sources needed since this diagram shows conceptual workflow, not actual code structure]

**Section sources**
- [Siswa.php](file://app/Models/Siswa.php)
- [NilaiKokurikuler.php](file://app/Models/NilaiKokurikuler.php)

### Activity Setup, Registration, and Scheduling Processes
- Setup:
  - Create organizations and assign activities.
  - Define clubs and associate advisors.
- Registration:
  - Students enroll in activities via membership records.
- Scheduling:
  - Activities are scheduled under organizations; advisors manage sessions.

```mermaid
sequenceDiagram
participant TU as "TU User"
participant OrgView as "Organisasi View"
participant OrgModel as "Organisasi Model"
participant EskulModel as "Eskul Model"
participant PEModel as "PembinaEskul Model"
TU->>OrgView : Create Organization
OrgView->>OrgModel : Persist organization
TU->>OrgView : Add Activity to Organization
OrgView->>EskulModel : Persist activity
TU->>OrgView : Assign Advisor to Activity
OrgView->>PEModel : Persist advisor record
```

**Diagram sources**
- [index.blade.php (TU Organisasi)](file://resources/views/tu/organisasi/index.blade.php)
- [Organisasi.php](file://app/Models/Organisasi.php)
- [Eskul.php](file://app/Models/Eskul.php)
- [PembinaEskul.php](file://app/Models/PembinaEskul.php)

**Section sources**
- [index.blade.php (TU Organisasi)](file://resources/views/tu/organisasi/index.blade.php)
- [Organisasi.php](file://app/Models/Organisasi.php)
- [Eskul.php](file://app/Models/Eskul.php)
- [PembinaEskul.php](file://app/Models/PembinaEskul.php)

### Integration with Student Profiles, Reports, and Dashboards
- Student profiles:
  - Student membership and achievements integrate with student records.
- Reports:
  - Export and report generation supported by services.
- Dashboards:
  - TU and Guru views provide oversight and action surfaces.

```mermaid
graph TB
SS["Siswa"]
SE["SiswaEskul"]
PR["Prestasi"]
NI["NilaiKokurikuler"]
EXP["ExportService"]
REP["RaporService"]
SS --> SE
SS --> PR
SS --> NI
EXP --> SS
REP --> SS
```

**Diagram sources**
- [Siswa.php](file://app/Models/Siswa.php)
- [SiswaEskul.php](file://app/Models/SiswaEskul.php)
- [Prestasi.php](file://app/Models/Prestasi.php)
- [NilaiKokurikuler.php](file://app/Models/NilaiKokurikuler.php)
- [ExportService.php](file://app/Services/ExportService.php)
- [RaporService.php](file://app/Services/RaporService.php)

**Section sources**
- [Siswa.php](file://app/Models/Siswa.php)
- [SiswaEskul.php](file://app/Models/SiswaEskul.php)
- [Prestasi.php](file://app/Models/Prestasi.php)
- [NilaiKokurikuler.php](file://app/Models/NilaiKokurikuler.php)
- [ExportService.php](file://app/Services/ExportService.php)
- [RaporService.php](file://app/Services/RaporService.php)

## Dependency Analysis
The extracurricular domain exhibits clear separation of concerns:
- Models encapsulate domain entities and relationships.
- Migrations define the schema and enforce referential integrity.
- Views provide role-specific dashboards.
- Services support export/reporting.

```mermaid
graph LR
M_E["Eskul Model"] --> M_SE["SiswaEskul Model"]
M_O["Organisasi Model"] --> M_E
M_PE["PembinaEskul Model"] --> M_E
M_DK["DeskripsiKokurikuler Model"] --> M_NI["NilaiKokurikuler Model"]
M_SS["Siswa Model"] --> M_SE
M_SS --> M_PR["Prestasi Model"]
V_TU_ORG["TU Organisasi View"] --> M_O
V_GURU_ORG["Guru Organisasi View"] --> M_O
V_TU_PRESTASI["TU Prestasi View"] --> M_PR
SVC_EXP["ExportService"] --> M_SS
SVC_REP["RaporService"] --> M_SS
```

**Diagram sources**
- [Eskul.php](file://app/Models/Eskul.php)
- [SiswaEskul.php](file://app/Models/SiswaEskul.php)
- [Organisasi.php](file://app/Models/Organisasi.php)
- [PembinaEskul.php](file://app/Models/PembinaEskul.php)
- [DeskripsiKokurikuler.php](file://app/Models/DeskripsiKokurikuler.php)
- [NilaiKokurikuler.php](file://app/Models/NilaiKokurikuler.php)
- [Siswa.php](file://app/Models/Siswa.php)
- [Prestasi.php](file://app/Models/Prestasi.php)
- [index.blade.php (TU Organisasi)](file://resources/views/tu/organisasi/index.blade.php)
- [index.blade.php (Guru Organisasi)](file://resources/views/guru/organisasi/index.blade.php)
- [index.blade.php (TU Prestasi)](file://resources/views/tu/prestasi/index.blade.php)
- [ExportService.php](file://app/Services/ExportService.php)
- [RaporService.php](file://app/Services/RaporService.php)

**Section sources**
- [Eskul.php](file://app/Models/Eskul.php)
- [SiswaEskul.php](file://app/Models/SiswaEskul.php)
- [Organisasi.php](file://app/Models/Organisasi.php)
- [PembinaEskul.php](file://app/Models/PembinaEskul.php)
- [DeskripsiKokurikuler.php](file://app/Models/DeskripsiKokurikuler.php)
- [NilaiKokurikuler.php](file://app/Models/NilaiKokurikuler.php)
- [Siswa.php](file://app/Models/Siswa.php)
- [Prestasi.php](file://app/Models/Prestasi.php)
- [index.blade.php (TU Organisasi)](file://resources/views/tu/organisasi/index.blade.php)
- [index.blade.php (Guru Organisasi)](file://resources/views/guru/organisasi/index.blade.php)
- [index.blade.php (TU Prestasi)](file://resources/views/tu/prestasi/index.blade.php)
- [ExportService.php](file://app/Services/ExportService.php)
- [RaporService.php](file://app/Services/RaporService.php)

## Performance Considerations
- Indexing: Ensure foreign keys and frequently queried columns (e.g., student ID, activity ID, organization ID) are indexed to optimize joins and lookups.
- Pagination: Apply pagination in dashboards and reports to avoid heavy result sets.
- Caching: Cache static lookup data such as organization lists and activity categories.
- Batch operations: Use batch inserts/updates for mass enrollment or advisor assignments.
- Reporting: Offload heavy report generation to queued jobs and leverage export services.

[No sources needed since this section provides general guidance]

## Troubleshooting Guide
Common issues and resolutions:
- Duplicate memberships: Validate uniqueness of student-activity combinations before insertion.
- Missing advisor assignments: Ensure advisor records exist for activities before scheduling sessions.
- Inconsistent status updates: Implement atomic transactions for enrollment and status changes.
- Report discrepancies: Verify descriptor-to-evaluation mappings and ensure academic year boundaries are respected.
- Dashboard filters: Confirm filters align with user roles and permissions.

[No sources needed since this section provides general guidance]

## Conclusion
The extracurricular module integrates activity setup, student membership, organization oversight, academic descriptors, and achievement recognition into a cohesive system. With role-specific dashboards and documented procedures, schools can efficiently manage clubs, sports programs, and organizations while maintaining academic balance and generating actionable reports.

[No sources needed since this section summarizes without analyzing specific files]

## Appendices
- Examples:
  - Managing a sports program: create an organization for sports, define teams/clubs, assign advisors, enable student registration, track participation, and evaluate descriptors.
  - Managing a debate club: create the organization and club, schedule weekly meetings, manage membership, and record descriptors and achievements.
- Best practices:
  - Clearly define activity categories and advisor responsibilities.
  - Establish transparent registration deadlines and communication channels.
  - Regularly review participation and academic performance trends.
  - Use certificates and achievement records to motivate continued engagement.
- References:
  - Procedures: [06-ekstra-organisasi.md](file://docs/manual-tu/06-ekstra-organisasi.md), [09-piket-organisasi.md](file://docs/manual-guru/09-piket-organisasi.md)
  - Routes and menus: [web.php](file://routes/web.php), [GuruMenuService.php](file://app/Services/GuruMenuService.php)

[No sources needed since this section aggregates previously cited materials]