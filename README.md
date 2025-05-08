Jedox Macro: Dynamic PALO.FILTER Generator
This repository provides a Jedox PHP macro to dynamically generate regular expressions (PerlExp) for the PALO.FILTER function. This is especially useful for cubes with many dimensions where users want to filter base elements beneath a consolidated element across multiple dimensions.

🚀 Purpose
Jedox cubes can contain high-dimensional structures with nested consolidations. This macro automates the extraction of base-level elements from any selected consolidated element and builds a dynamic filter expression (~|element1|element2|...) for use in PALO formulas, improving performance and flexibility in report-building.

📋 Features
Recursively extracts all base-level elements under any selected consolidated element.

Returns:

PerlExp strings for PALO.FILTER-compatible formulas

An array representation of all base elements

Execution time to help monitor performance

Filters across 7 dimensions:

Company

DDC (Div-Dept-Cost Center)

Classification

Line of Business (LOB)

Sub Line of Business

Business Type

Location

🧠 How It Works
Start From Consolidated Element
The macro checks if a selected element is consolidated.

Traverse Down
It recursively calls Jedox functions PALO_ELEMENT_LIST_CHILDREN and PALO_ETYPE to get all descendant base elements.

Generate PerlExp
Uses the format ~|Base1|Base2|... for filtering in Jedox reports.

Output Results
Writes both array and string outputs to the "Backend" sheet in specified cells.

🧩 File Structure
getbaseelements()
Core recursive function to traverse the dimension tree.

getchildren() / getconsolidated()
Wrapper helpers to call Jedox functions and format outputs.

buildperlexp()
Main driver function that:

Reads input from the spreadsheet

Triggers the base element retrieval

Builds PerlExp filters

Writes back results to the spreadsheet

📌 Prerequisites
Jedox Integrator enabled

PHP macro support in Jedox

Sheet named Backend with expected structure:

Server/database defined at C2

Dimensions listed from B9:B15

Corresponding selected elements in C9:C15

Optional filter checkbox in F19

📤 Output Cells
PerlExp filters → F9:F15

Base element arrays → E9:E15

Execution time → F18

✅ Example Output
Dimension	Consolidated Element	Base Elements	PerlExp Filter
Company	TDSC	{TDSC1, TDSC2}	`~
DDC	901-02-21	{901-02-21-01, -02, -03}	`~

🛠️ Customization Tips
You can expand this to support additional dimensions by adding rows in the backend sheet and replicating the logic.

Adjust $max_exec_time in getbaseelements() if you expect large dimension trees.

⚠️ Notes
The macro includes a timer to prevent infinite loops in deeply nested hierarchies.

Performance depends on the number of elements under a consolidation.

📄 License
MIT License
