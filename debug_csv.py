import csv

# Path to your CSV file
file_path = "data/total_local_food_production.csv"

# Open and read the file
with open(file_path, mode='r', encoding='utf-8') as csvfile:
    # Use comma delimiter
    reader = csv.reader(csvfile, delimiter=',')
    header = next(reader)  # Read the header row
    print("Header row:", header)
    
    # Read and validate rows
    for row_number, row in enumerate(reader, start=1):
        if len(row) == len(header):  # Ensure row length matches header
            print(f"Row {row_number}: {row}")
        else:
            print(f"Warning: Row {row_number} has insufficient columns. Skipping.")
