import json
import mysql.connector
from collections import Counter

# 1) DB connection (XAMPP default)
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="canteen_db"
)

cursor = conn.cursor(dictionary=True)

# 2) Fetch all order items
cursor.execute("SELECT product, quantity FROM order_items")
items = cursor.fetchall()

# 3) Count total sold per product
product_counter = Counter()
for row in items:
    product_counter[row["product"]] += int(row["quantity"])

top_items = product_counter.most_common(5)

# 4) Total orders + revenue
cursor.execute("SELECT COUNT(*) as total_orders, COALESCE(SUM(total),0) as total_revenue FROM orders")
stats = cursor.fetchone()

# 5) Prepare result
result = {
    "total_orders": int(stats["total_orders"]),
    "total_revenue": int(stats["total_revenue"]),
    "top_items": [{"product": p, "qty": q} for p, q in top_items]
}
# ---- Recommendations (simple combos) ----
cursor.execute("SELECT order_id, product FROM order_items")
rows = cursor.fetchall()

# order_id -> set(products)
orders_map = {}
for r in rows:
    oid = r["order_id"]
    prod = r["product"]
    orders_map.setdefault(oid, set()).add(prod)

# pair counter: (A,B) count
pair_counter = Counter()

for oid, prods in orders_map.items():
    prods = sorted(list(prods))
    for i in range(len(prods)):
        for j in range(i + 1, len(prods)):
            pair_counter[(prods[i], prods[j])] += 1

top_combos = pair_counter.most_common(5)

result["top_combos"] = [
    {"item1": a, "item2": b, "count": c}
    for (a, b), c in top_combos
]

# 6) Save JSON in same folder
import os
script_dir = os.path.dirname(os.path.abspath(__file__))
json_path = os.path.join(script_dir, "analysis.json")

with open(json_path, "w") as f:
    json.dump(result, f, indent=4)

print("analysis.json generated successfully!")

cursor.close()
conn.close()
