# Graph Report - C:/xampp/htdocs/dcc-lib  (2026-07-22)

## Corpus Check
- 294 files · ~296,159 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 871 nodes · 1024 edges · 215 communities (116 shown, 99 thin omitted)
- Extraction: 91% EXTRACTED · 9% INFERRED · 0% AMBIGUOUS · INFERRED: 94 edges (avg confidence: 0.81)
- Token cost: 47,961 input · 662 output

## Community Hubs (Navigation)
- Admin Management System
- Agent Architecture
- Database Architecture
- Agent Architecture
- Authentication & Authorization
- Database Architecture
- Agent Architecture
- Community 7
- Agent Architecture
- Agent Architecture
- Agent Architecture
- Scanner & Research Tools
- Admin Management System
- Agent Architecture
- Agent Architecture
- Community 15
- Community 16
- Community 17
- Community 18
- Community 19
- Community 20
- Community 21
- Community 22
- Community 23
- Community 24
- Community 25
- Community 26
- Community 27
- Community 28
- Community 29
- Community 30
- Community 31
- Community 32
- Community 33
- Community 34
- Community 35
- Community 36
- Community 37
- Community 38
- Community 39
- Community 40
- Community 41
- Community 42
- Community 43
- Community 44
- Community 45
- Community 46
- Community 47
- Community 85
- Community 86
- Community 87
- Community 88
- Community 89
- Community 90
- Community 91
- Community 92
- Community 93
- Community 94
- Community 95
- Community 96
- Community 97
- Community 98
- Community 99
- Community 100
- Community 101
- Community 102
- Community 103
- Community 104
- Community 105
- Community 106
- Community 109
- Community 110
- Community 111
- Community 112
- Community 113
- Community 114
- Community 115
- Community 116
- Community 117
- Community 118
- Community 119
- Community 120
- Community 121
- Community 122
- Community 123
- Community 124
- Community 125
- Community 126
- Community 127
- Community 128
- Community 129
- Community 130
- Community 131
- Community 132
- Community 133
- Community 134
- Community 135
- Community 136
- Community 137
- Community 138
- Community 139
- Community 140
- Community 141
- Community 142
- Community 143
- Community 144
- Community 145
- Community 146
- Community 147
- Community 148
- Community 149
- Community 150
- Community 151
- Community 152
- Community 153
- Community 154
- Community 155
- Community 156
- Community 157
- Community 158
- Community 159
- Community 160
- Community 161
- Community 162
- Community 163
- Community 164
- Community 165
- Community 166
- Community 167
- Community 168
- Community 169
- Community 170
- Community 171
- Community 172
- Community 173
- Community 174
- Community 177
- Community 187
- Community 188
- Community 189
- Community 191
- Community 192

## God Nodes (most connected - your core abstractions)
1. `AdminController` - 25 edges
2. `LibraryController` - 21 edges
3. `Student` - 20 edges
4. `Employee` - 15 edges
5. `User` - 14 edges
6. `Research` - 12 edges
7. `Mobile Design Skill` - 12 edges
8. `DesignSystemGenerator` - 11 edges
9. `PerformanceChecker` - 11 edges
10. `Book` - 11 edges

## Surprising Connections (you probably didn't know these)
- `Deploy Workflow` --conceptually_related_to--> `GitHub Actions Deploy Workflow`  [INFERRED]
  .agent/workflows/deploy.md → .github/workflows/deploy.yml
- `Explorer Agent` --conceptually_related_to--> `Orchestrator Agent`  [INFERRED]
  .agent/agents/explorer-agent.md → .agent/agents/orchestrator.md
- `QA Automation Engineer Agent` --conceptually_related_to--> `Test Engineer Agent`  [INFERRED]
  .agent/agents/qa-automation-engineer.md → .agent/agents/test-engineer.md
- `Product Manager Agent` --semantically_similar_to--> `Product Owner Agent`  [INFERRED] [semantically similar]
  .agent/agents/product-manager.md → .agent/agents/product-owner.md
- `Zero-Downtime Migration` --semantically_similar_to--> `Blue-Green Deployment`  [INFERRED] [semantically similar]
  .agent/skills/database-design/migrations.md → .agent/skills/deployment-procedures/SKILL.md

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Security Specialist Team** — agent_agents_security_auditor, agent_agents_penetration_tester, concept_owasp_top_10_2025 [EXTRACTED 1.00]
- **Testing Specialist Team** — agent_agents_test_engineer, agent_agents_qa_automation_engineer, concept_testing_pyramid, concept_tdd_workflow [EXTRACTED 1.00]
- **Product Management Team** — agent_agents_product_manager, agent_agents_product_owner, concept_moscow_prioritization [EXTRACTED 1.00]
- **Database Design Skills Group** — _agent_skills_database_design_database_selection_postgresql, _agent_skills_database_design_indexing_btree_index, _agent_skills_database_design_orm_selection_drizzle, _agent_skills_database_design_schema_design_normalization, _agent_skills_database_design_optimization_n_plus_one, _agent_skills_database_design_migrations_zero_downtime [EXTRACTED 1.00]
- **UX Psychology Laws** — _agent_skills_frontend_design_ux_psychology_hicks_law, _agent_skills_frontend_design_ux_psychology_fitts_law, _agent_skills_frontend_design_ux_psychology_millers_law, _agent_skills_frontend_design_ux_psychology_gestalt_proximity [EXTRACTED 1.00]
- **Game Development Design Patterns** — _agent_skills_game_development_game_loop, _agent_skills_game_development_state_machine, _agent_skills_game_development_object_pooling, _agent_skills_game_development_ecs [EXTRACTED 1.00]
- **Game Development Platform Skills** — agent_skills_game_development_mobile_games_skill, agent_skills_game_development_pc_games_skill, agent_skills_game_development_web_games_skill, agent_skills_game_development_vr_ar_skill [INFERRED 0.95]
- **Mobile Design System Components** — agent_skills_mobile_design_mobile_color_system, agent_skills_mobile_design_mobile_typography, agent_skills_mobile_design_mobile_navigation, agent_skills_mobile_design_platform_android [INFERRED 0.95]
- **Mobile Development Lifecycle** — agent_skills_mobile_design_mobile_design_thinking, agent_skills_mobile_design_mobile_backend, agent_skills_mobile_design_mobile_testing, agent_skills_mobile_design_mobile_debugging, agent_skills_mobile_design_mobile_performance [INFERRED 0.85]
- **Next.js Bundle Optimization Strategy** — agent_skills_nextjs_react_expert_2_bundle_bundle_size_optimization_barrel_imports, agent_skills_nextjs_react_expert_2_bundle_bundle_size_optimization_dynamic_imports, agent_skills_nextjs_react_expert_skill_performance_optimization [INFERRED 0.85]
- **Mobile Touch Interaction Design System** — agent_skills_mobile_design_touch_psychology_fitts_law, agent_skills_mobile_design_touch_psychology_thumb_zone, agent_skills_mobile_design_touch_psychology_haptic_feedback [EXTRACTED 1.00]
- **React State Management Optimization Patterns** — agent_skills_nextjs_react_expert_5_rerender_re_render_optimization_derived_state, agent_skills_nextjs_react_expert_5_rerender_re_render_optimization_functional_setstate, agent_skills_nextjs_react_expert_5_rerender_re_render_optimization_useref [INFERRED 0.90]
- **TDD Testing Workflow** — agent_skills_tdd_workflow_skill_red_green_refactor, agent_skills_tdd_workflow_skill_three_laws_tdd, agent_skills_tdd_workflow_skill_aaa_pattern [EXTRACTED 1.00]
- **Security Audit System** — agent_skills_vulnerability_scanner_skill_owasp_top_10_2025, agent_skills_vulnerability_scanner_skill_attack_surface_mapping, agent_skills_vulnerability_scanner_skill_risk_prioritization, agent_skills_vulnerability_scanner_checklists_owasp_checklist [EXTRACTED 1.00]
- **Web Application Testing Suite** — agent_skills_webapp_testing_skill_deep_audit, agent_skills_webapp_testing_skill_e2e_testing, agent_skills_webapp_testing_skill_playwright, agent_skills_webapp_testing_skill_visual_testing [EXTRACTED 1.00]

## Communities (215 total, 99 thin omitted)

### Community 0 - "Admin Management System"
Cohesion: 0.06
Nodes (15): AdminController, AuthController, Controller, LibraryController, ScannerController, Book, BookElem, Employee (+7 more)

### Community 1 - "Agent Architecture"
Cohesion: 0.05
Nodes (42): BM25, detect_domain(), _load_csv(), BM25 ranking algorithm for text search, Lowercase, split, remove punctuation, filter short words, Build BM25 index from documents, Score all documents against query, Load CSV and return list of dicts (+34 more)

### Community 2 - "Database Architecture"
Cohesion: 0.05
Nodes (42): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+34 more)

### Community 3 - "Agent Architecture"
Cohesion: 0.10
Nodes (29): Colors, main(), print_error(), print_final_report(), print_header(), print_step(), print_success(), print_warning() (+21 more)

### Community 4 - "Authentication & Authorization"
Cohesion: 0.10
Nodes (9): ResearchController, Research, Illuminate\Database\Eloquent\Relations\BelongsTo, Illuminate\Database\Eloquent\Relations\HasMany, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, ExampleTest, ResearchTest (+1 more)

### Community 5 - "Database Architecture"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 6 - "Agent Architecture"
Cohesion: 0.11
Nodes (25): Backend Specialist Agent, Code Archaeologist Agent, Database Architect Agent, DevOps Engineer Agent, Explorer Agent, Frontend Specialist Agent, Mobile Developer Agent, Orchestrator Agent (+17 more)

### Community 7 - "Community 7"
Cohesion: 0.10
Nodes (19): axios, concurrently, laravel-vite-plugin, devDependencies, axios, concurrently, laravel-vite-plugin, tailwindcss (+11 more)

### Community 8 - "Agent Architecture"
Cohesion: 0.16
Nodes (9): main(), PerformanceChecker, Check for data fetching in useEffect (Section 4), Check for missing React.memo, useMemo, useCallback (Section 5), Check for unoptimized images (Section 6), Generate final report, Check for sequential await patterns (Section 1), Check for barrel imports (Section 2) (+1 more)

### Community 9 - "Agent Architecture"
Cohesion: 0.27
Nodes (14): check_script_exists(), Colors, main(), print_error(), print_header(), print_step(), print_success(), print_summary() (+6 more)

### Community 10 - "Agent Architecture"
Cohesion: 0.15
Nodes (14): Game Design Skill, Mobile Games Skill, i18n Localization Skill, Mobile Design Decision Trees, Mobile Backend Guide, Mobile Color System, Mobile Debugging Guide, Mobile Design Thinking (+6 more)

### Community 11 - "Scanner & Research Tools"
Cohesion: 0.27
Nodes (12): main(), Any, Validate no hardcoded secrets (OWASP A04).     Checks: API keys, tokens, passwor, Validate dangerous code patterns (OWASP A05).     Checks: Injection risks, XSS,, Validate security configuration (OWASP A02).     Checks: Security headers, CORS,, Execute security validation scans., Validate supply chain security (OWASP A03).     Checks: npm audit, lock file pre, run_full_scan() (+4 more)

### Community 12 - "Admin Management System"
Cohesion: 0.22
Nodes (7): User, DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Seeder, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 13 - "Agent Architecture"
Cohesion: 0.27
Nodes (11): generate_section_file(), group_rules_by_section(), main(), parse_frontmatter(), parse_rule_file(), Path, Group all rules by their section prefix, Generate a merged section file (+3 more)

### Community 14 - "Agent Architecture"
Cohesion: 0.29
Nodes (10): check_hardcoded_strings(), check_locale_completeness(), find_locale_files(), flatten_keys(), main(), Path, Flatten nested dict keys., Check for hardcoded strings in code files. (+2 more)

### Community 15 - "Community 15"
Cohesion: 0.27
Nodes (6): StudentsImport, Maatwebsite\Excel\Concerns\ToModel, Maatwebsite\Excel\Concerns\WithBatchInserts, Maatwebsite\Excel\Concerns\WithChunkReading, Maatwebsite\Excel\Concerns\WithHeadingRow, Maatwebsite\Excel\Concerns\WithUpserts

### Community 16 - "Community 16"
Cohesion: 0.50
Nodes (8): analyze_package_json(), count_files(), detect_features(), get_project_root(), main(), print_status(), Any, Path

### Community 17 - "Community 17"
Cohesion: 0.39
Nodes (8): check_api_code(), check_openapi_spec(), find_api_files(), main(), Path, Find API-related files., Check OpenAPI/Swagger specification., Check API code for common issues.

### Community 18 - "Community 18"
Cohesion: 0.39
Nodes (8): check_page(), find_web_pages(), is_page_file(), main(), Path, Check a single web page for GEO elements., Check if this file is likely a public-facing page., Find public-facing web pages only.

### Community 19 - "Community 19"
Cohesion: 0.39
Nodes (8): check_page(), find_pages(), is_page_file(), main(), Path, Check if this file is likely a public-facing page., Find page files to check., Check a single page for SEO issues.

### Community 20 - "Community 20"
Cohesion: 0.54
Nodes (7): get_project_root(), get_start_command(), is_running(), main(), start_server(), status_server(), stop_server()

### Community 21 - "Community 21"
Cohesion: 0.48
Nodes (6): find_schema_files(), main(), Path, Find database schema files., Validate Prisma schema file., validate_prisma_schema()

### Community 22 - "Community 22"
Cohesion: 0.48
Nodes (6): check_accessibility(), find_html_files(), main(), Path, Find all HTML/JSX/TSX files., Check a single file for accessibility issues.

### Community 24 - "Community 24"
Cohesion: 0.48
Nodes (6): check_python_coverage(), check_typescript_coverage(), main(), Path, Check TypeScript type coverage., Check Python type hints coverage.

### Community 26 - "Community 26"
Cohesion: 0.47
Nodes (3): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 27 - "Community 27"
Cohesion: 0.53
Nodes (6): DCC Basic Education Campus, Davao Central College, Free Tuition and Miscellaneous Fees Program, DCC Cover Image, School ID 405438, DCC Vision Statement

### Community 28 - "Community 28"
Cohesion: 0.50
Nodes (5): Performance Optimizer Agent, SEO Specialist Agent, Core Web Vitals, E-E-A-T Framework, SEO vs GEO

### Community 29 - "Community 29"
Cohesion: 0.40
Nodes (5): Defer Await Until Needed, Avoid Barrel File Imports, Dynamic Imports for Heavy Components, Parallel Data Fetching with Component Composition, React Performance Optimization Framework

### Community 30 - "Community 30"
Cohesion: 0.50
Nodes (4): get_summary(), Run Lighthouse audit on URL., Generate summary based on scores., run_lighthouse()

### Community 31 - "Community 31"
Cohesion: 0.40
Nodes (5): AAA Pattern (Arrange-Act-Assert), RED-GREEN-REFACTOR Cycle, AAA Pattern (Arrange-Act-Assert), Debug Mode, Test Generation and Execution Workflow

### Community 33 - "Community 33"
Cohesion: 0.50
Nodes (4): Neon, pgvector, PostgreSQL, GIN Index

### Community 34 - "Community 34"
Cohesion: 0.50
Nodes (4): OWASP Top 10 Audit Checklist, Exceptional Conditions (A10), OWASP Top 10:2025, Supply Chain Security (A03)

### Community 35 - "Community 35"
Cohesion: 0.50
Nodes (4): Create Application Workflow, Multi-Agent Orchestration Mode, Two-Phase Orchestration (Planning + Implementation), Plan Workflow

### Community 38 - "Community 38"
Cohesion: 0.67
Nodes (3): Zero-Downtime Migration, Blue-Green Deployment, Canary Deployment

### Community 39 - "Community 39"
Cohesion: 0.67
Nodes (3): N+1 Query Problem, Drizzle ORM, Prisma ORM

### Community 40 - "Community 40"
Cohesion: 0.67
Nodes (3): Product Manager Agent, Product Owner Agent, MoSCoW Prioritization Framework

### Community 41 - "Community 41"
Cohesion: 0.67
Nodes (3): Promise.all() for Independent Operations, Node.js Async Patterns, Python Async vs Sync Decision

### Community 42 - "Community 42"
Cohesion: 0.67
Nodes (3): Per-Request Deduplication with React.cache(), Use SWR for Automatic Deduplication, Cache Repeated Function Calls

### Community 43 - "Community 43"
Cohesion: 0.67
Nodes (3): Calculate Derived State During Rendering, Use Functional setState Updates, Use useRef for Transient Values

### Community 44 - "Community 44"
Cohesion: 0.67
Nodes (3): Testing Pyramid, E2E Testing Principles, Playwright Testing Framework

### Community 45 - "Community 45"
Cohesion: 0.67
Nodes (3): Deploy Workflow, Pre-Deployment Checklist, GitHub Actions Deploy Workflow

### Community 46 - "Community 46"
Cohesion: 0.67
Nodes (3): Enhance Workflow, Preview Management, Status Display Workflow

### Community 47 - "Community 47"
Cohesion: 0.67
Nodes (3): Davao Central College, Educational Symbolism, Davao Central College Logo

## Knowledge Gaps
- **235 isolated node(s):** `Colors`, `Colors`, `$schema`, `name`, `type` (+230 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **99 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Student` connect `Admin Management System` to `Community 15`?**
  _High betweenness centrality (0.006) - this node is a cross-community bridge._
- **Why does `User` connect `Admin Management System` to `Admin Management System`?**
  _High betweenness centrality (0.004) - this node is a cross-community bridge._
- **What connects `Colors`, `Colors`, `$schema` to the rest of the system?**
  _235 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Admin Management System` be split into smaller, more focused modules?**
  _Cohesion score 0.05733397037744864 - nodes in this community are weakly interconnected._
- **Should `Agent Architecture` be split into smaller, more focused modules?**
  _Cohesion score 0.05263157894736842 - nodes in this community are weakly interconnected._
- **Should `Database Architecture` be split into smaller, more focused modules?**
  _Cohesion score 0.046511627906976744 - nodes in this community are weakly interconnected._
- **Should `Agent Architecture` be split into smaller, more focused modules?**
  _Cohesion score 0.10037878787878787 - nodes in this community are weakly interconnected._