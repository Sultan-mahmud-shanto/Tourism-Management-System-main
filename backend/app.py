from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route('/recommend', methods=['POST'])
def recommend():
    data = request.get_json()
    print("📦 Received data from PHP:", data)  # <---- Add this line for debugging

    if not data:
        return jsonify({"message": "No data received"}), 400

    # Simple logic (replace with your actual AI logic)
    recommendation = {
        "place": data["preference"].title(),
        "estimated_cost": int(data["budget"]) - 500,
        "estimated_days": data["days"],
        "food": data["food"],
        "rating": 4.5
    }

    return jsonify(recommendation)

if __name__ == '__main__':
    app.run(debug=True)
