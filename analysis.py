import json
import mysql.connector
from collections import Counter

conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="canteen_db"
)

cursor = conn.cursor(dictionary=True)

cursor.execute("SELECT product, quantity FROM order_items")
items = cursor.fetchall()

product_counter = Counter()
for row in items:
    product_counter[row["product"]] += int(row["quantity"])

top_items = product_counter.most_common(5)

cursor.execute("SELECT COUNT(*) as total_orders, COALESCE(SUM(total),0) as total_revenue FROM orders")
stats = cursor.fetchone()

result = {
    "total_orders": int(stats["total_orders"]),
    "total_revenue": int(stats["total_revenue"]),
    "top_items": [{"product": p, "qty": q} for p, q in top_items]
}

with open("analysis.json", "w") as f:
    json.dump(result, f, indent=4)

print("analysis.json generated successfully!")

cursor.close()
conn.close()
