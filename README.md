# SmartCard Navigator

## Table of Contents
1. Introduction
2. Strategy & Planning
3. Technology Stack
4. Database Design
5. Backend Implementation
6. Frontend Implementation
7. Installation Instructions
8. Automation
9. Future Enhancements

---

## 1. Introduction
SmartCard Navigator is designed to assist customers in selecting the most suitable credit card based on predefined sorting criteria. The system fetches credit card data, processes it efficiently, and presents it in an optimized manner to end-users. The primary sorting logic is based on price, with the cheapest options displayed first.

## 2. Strategy & Planning
### Data Source
The credit card data is retrieved from an external API:
`https://tools.financeads.net/webservice.php?wf=1&format=xml&calc=kreditkarterechner&country=ES`

### Data Processing
- **Fetching Data:** The API is queried to obtain updated credit card information.
- **Storing Data:** The retrieved data is imported into a relational database, structured for future scalability.
- **Update Frequency:** Given the estimated volume of 300 credit cards in the Spanish market, the import process runs once daily at low-traffic hours (e.g., 2 A.M.).
- **Change Management:** The system maintains an API log table to store raw requests and responses, ensuring data integrity and preventing loss.
- **Data Editing:** Admin modifications take precedence over API updates. Original values are stored in JSON fields to track them while allowing necessary refinements and unifications. This denormalized structure ensures faster queries.
- **Sorting & Caching:** After data import, sorting is performed based on predefined price criteria, and the results are cached for improved response times.

## 3. Technology Stack
- **Version Control:** Private Git repositories for backend (`creditcompare-api`) and frontend (`creditcompare-ui`).
- **Backend:** Symfony, API Platform, Doctrine ORM.
- **Frontend:** Vue.js 3, Tailwind CSS, Vue Router.
- **Database:** MySQL with indexing for optimized query performance.
- **Caching:** Redis for efficient data retrieval.
- **Infrastructure:** Docker for consistent environment setup.
- **Task Scheduling:** Symfony Messenger and Scheduler instead of traditional cron jobs.

## 4. Database Design
- **Core Table:** `credit_cards` (Primary entity for credit card data).
- **Supporting Tables:**
  - `api_logs`: Stores raw API responses.
  - `card_types`, `banks`, `credit_card_images`, `credit_card_features`: Stores credit card-related metadata.
  - `currencies`: Manages site-wide currency settings.
  - `editables`: Defines which columns are editable by staff.
  - `users`, `roles`, `permissions`: Handles admin access control.
  - `sorting_criteria`: Stores sorting logic defined by admins or the market team.

## 5. Backend Implementation
- **Commands:**
  - `ImportCreditCardsCommand`: Fetches and imports data daily.
  - `SortCreditCardsCommand`: Sorts and caches credit card data daily.
- **API Controller:** `CreditCardController` handles all client-side requests.
- **ORM:** Doctrine ORM for database queries and repositories.
- **Error Handling:** Implemented robust validation and logging mechanisms.
- **API Access:** `http://localhost:8080/` (Local environment).

## 6. Frontend Implementation
- **Framework:** Using Vue Notus template, the frontend leverages Vue.js 3 for a fast development experience. Vue Router manages navigation, and Tailwind CSS ensures a modern UI.
- **Client-Side Functionalities:**
  - Displays sorted credit card listings.
  - Allows sorting and filtering (handled on the frontend).
  - Supports inline admin edits on predefined fields in the `editables` table.
- **Admin Panel:** Accessible via `/admin/credit-cards`, with editable fields underlined based on `editables` table settings.
- **Frontend URL:** `http://localhost:5000/`.

## 7. Installation Instructions
1. Extract the provided ZIP file.
2. Navigate to each project directory (`creditcompare-api` and `creditcompare-ui`).
3. Run the following command inside each directory:
   ```sh
   docker-compose up -d --build
   ```
4. The system will initialize and become accessible at the specified local URLs.

## 8. Automation
The installation process is fully automated with Docker. Running the provided `docker-compose` command will:
- Install all dependencies and project packages.
- Build both backend and frontend projects.
- Execute database migrations.
- Load predefined fixture data for testing and development.
- Automatically import credit card data into the database after setup is complete.

This ensures a streamlined one-command setup and allows for quick deployment in various environments, ensuring everything is ready to use without manual intervention.

## 9. Future Enhancements
- **Machine Learning-Based Recommendation Engine** to suggest the best credit cards based on user behavior.
- **Additional Filtering Options** to enhance user experience.
- **Advanced Analytics Dashboard** for admins to gain insights into user preferences and market trends.
- **Full Authentication & Authorization System** for improved security and user role management.
- **Comprehensive Test Suite** to ensure application stability and performance.
- **CI/CD Pipeline Setup** for streamlined deployments.
- **Production Deployment** to showcase the demo on a live server.

---
**Conclusion**
SmartCard Navigator provides a well-architected solution for credit card comparison, ensuring scalability, efficiency, and a seamless user experience. The foundation is strong, allowing for continuous improvements and expansion as needed.
