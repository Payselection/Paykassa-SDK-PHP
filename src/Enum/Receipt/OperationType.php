<?php

namespace PayKassa\Enum\Receipt;

enum OperationType: string
{
    case INCOME = 'Income';
    case EXPENSE = 'Expense';
    case INCOME_RETURN = 'IncomeReturn';
    case EXPENSE_RETURN = 'ExpenseReturn';
    case CORRECTION_INCOME = 'CorrectionIncome';
    case CORRECTION_EXPENSE = 'CorrectionExpense';
}
