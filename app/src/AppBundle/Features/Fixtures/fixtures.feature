@fixtures
Feature: Load basic project

  Scenario: Truncate table
      Given Truncated tables
          | f_users |
      And There are records in table 'f_users'
          | id | name |
          | 1  | Alex |
      And Records in 'f_users' where 'id = 1', has data
          | id | name |
          | 1  | Alex |