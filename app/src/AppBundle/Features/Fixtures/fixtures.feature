@fixtures
Feature: Load basic project

  Scenario: Truncate table
      Given Truncated tables
          | f_users |
      And There are items in table 'f_users'
          | id | name |
          | 1  | Alex |