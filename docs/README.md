# Parse XBRL based SEC Report JSON Data

## Gathers JSON info for public companies
- works with an unlimited number of JSON files
- organizes for SQL and Blockchain insertion
- stores into MYSQL and/or compatible, and compatible Blockchain (EVM based)
- tag keys and values with customizable values for later use 

### Workflow
- Report Processor turns all downloaded JSON files into SPL file objects
- Report processor delivers file objects one at a time to File Processor  
- File processor delivers 1 character at a time to State Machine
- State machine evaluates each character and changes state according to rules then
- State machine advises File processor to continue or provide next character  
- File Processor delivers actionable characters to Record Builder  
- Record Builder processes characters and updates Record, Data and Entry classes
    - Record Builder accumulates characters into keys and values  
    - Record Builder changes State as needed and/or creates new Data and Entry classes
    - Record Builder uses TaxonomyTerms class to assign keys and values to Data and Entry properties
- TaxonomyTerms keeps database of terms, adds new unknown terms automatically  
- Record datastore holds all Data class objects (~600 per JSON file)
- Data entrystore holds all Entry class objects (~5000 per JSON file)
- Database worker stores info into correct SQL tables (TODO)
- Blockchain worker enters info into Ethereum based blockchain(s) (TODO)

### Status
- Original commit: June 20, 2021
- Version: 0.2.0
- Not in working status yet
- Reads 14000 discrete corporate files in a single job, so far
- ready for database table creation and testing

### Objectives
- Abstracts away the need to understand XBRL or its current/future changes
- Automate daily report gathering (over 14,000 per business day)
- Make data ready for analysis, comparisons, conclusions
- Make data ready for supporting decisions and action steps

### What it is NOT
- not trading software
- not complete yet
- not useful unless you know what you are doing

### LICENSE
- MIT
- use it at your own risk. No guarantees of ANY KIND. 
- Licenses of other authors included. What they offer is their business, not ours. 

### HISTORY
- version 0.2.0 (BetterApproach branch) - July 1, 2021
- version 0.1.0 - (Master branch)  - June 20, 2021
