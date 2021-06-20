# Parse SEC Reports

## Gathers JSON info for > 14000 public companies
- organizes for SQL insertion
- stores into MYSQL or compatible
- tags key data with qualitative values for later use

### How it works
- State machine evaluates JSON files character by character and tells
- Record, Data and Entry classes how to organize info for each company
- Then database worker stores into correct tables 

### Status
- Original commit: June 20, 2021
- Version: 0.1.0
- Not in working status yet

### Objectives
- Automate daily report gathering (over 14,000 per business day)
- Make data ready for analysis, comparisons, conclusions
- Make data ready for supporting decisions and action steps

### What it is NOT
- not trading software
- not complete yet
- not useful unless you know what you are doing

### LICENSE
- use it at your own risk. No guarantees of ANY KIND
- Licenses of other authors included. What they offer is their business, not ours. 

