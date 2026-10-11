import sys
import json
import pandas as pd
import random
import datetime # Added for potential future date considerations, not used in current simple logic

# --- Dummy Data (Replace with your actual database/data sources) ---
# In a real system, you'd fetch this from a database or a more robust data store.
# This data includes Bangladesh-specific locations and details,
# with 'travel_from_dhaka_cost' and 'travel_from_dhaka_hours' for simple proximity simulation.
# Note: costs and times are indicative and should be replaced with actual data.
attractions_data = {
    "Sea": [
        {"name": "Cox's Bazar Beach", "location": "Cox's Bazar", "type": "Sea", "days_needed": 2, "cost_per_day": 3000, "travel_from_dhaka_cost": 2500, "travel_from_dhaka_hours": 10, "description": "Longest natural sea beach in the world. Enjoy sunset, parasailing, and fresh seafood."},
        {"name": "Saint Martin's Island", "location": "Teknaf (requires ferry)", "type": "Sea", "days_needed": 2, "cost_per_day": 4000, "travel_from_dhaka_cost": 3500, "travel_from_dhaka_hours": 12, "description": "Bangladesh's only coral island. Crystal clear water, serene beaches."},
        {"name": "Kuakata Beach", "location": "Patuakhali", "type": "Sea", "days_needed": 2, "cost_per_day": 2500, "travel_from_dhaka_cost": 2000, "travel_from_dhaka_hours": 8, "description": "Known for both sunrise and sunset views over the Bay of Bengal."},
    ],
    "Mountain": [
        {"name": "Bandarban (Nilgiri, Sangu River)", "location": "Bandarban", "type": "Mountain", "days_needed": 3, "cost_per_day": 3500, "travel_from_dhaka_cost": 3000, "travel_from_dhaka_hours": 9, "description": "Hilly region with stunning views, indigenous cultures, and waterfalls."},
        {"name": "Khagrachari (Sajek Valley)", "location": "Khagrachari", "type": "Mountain", "days_needed": 2, "cost_per_day": 3000, "travel_from_dhaka_cost": 2800, "travel_from_dhaka_hours": 8, "description": "The 'Queen of Hills', famous for clouds and serene landscapes."},
        {"name": "Rangamati (Kaptai Lake)", "location": "Rangamati", "type": "Mountain", "days_needed": 2, "cost_per_day": 2800, "travel_from_dhaka_cost": 2500, "travel_from_dhaka_hours": 7, "description": "Known for its beautiful lake, hanging bridge, and tribal culture."},
    ],
    "Forest": [
        {"name": "Sundarbans (Mangrove Forest)", "location": "Bagerhat", "type": "Forest", "days_needed": 3, "cost_per_day": 5000, "travel_from_dhaka_cost": 4000, "travel_from_dhaka_hours": 10, "description": "World's largest mangrove forest, home to the Royal Bengal Tiger."},
        {"name": "Lawachara National Park", "location": "Sylhet", "type": "Forest", "days_needed": 1, "cost_per_day": 1500, "travel_from_dhaka_cost": 1200, "travel_from_dhaka_hours": 6, "description": "Lush evergreen forest, home to diverse wildlife and rare birds."},
    ],
    "River": [
        {"name": "Ratargul Swamp Forest", "location": "Sylhet", "type": "River", "days_needed": 1, "cost_per_day": 2000, "travel_from_dhaka_cost": 1500, "travel_from_dhaka_hours": 6, "description": "A freshwater swamp forest, best explored by boat during monsoon."},
        {"name": "Meghna River (near Dhaka)", "location": "Narayanganj (nearby)", "type": "River", "days_needed": 1, "cost_per_day": 1800, "travel_from_dhaka_cost": 500, "travel_from_dhaka_hours": 1, "description": "Enjoy boat rides, river cruises, and local village life."},
    ],
    "Heritage": [
        {"name": "Ahsan Manzil", "location": "Dhaka", "type": "Heritage", "days_needed": 0.5, "cost_per_day": 1000, "travel_from_dhaka_cost": 0, "travel_from_dhaka_hours": 0.5, "description": "Pink Palace, a historical and architectural landmark of Dhaka."},
        {"name": "Lalbagh Fort", "location": "Dhaka", "type": "Heritage", "days_needed": 0.5, "cost_per_day": 1000, "travel_from_dhaka_cost": 0, "travel_from_dhaka_hours": 0.5, "description": "Unfinished 17th-century Mughal fort in Dhaka."},
        {"name": "Sixty Dome Mosque", "location": "Bagerhat", "type": "Heritage", "days_needed": 1, "cost_per_day": 1200, "travel_from_dhaka_cost": 2000, "travel_from_dhaka_hours": 8, "description": "UNESCO World Heritage site, one of the most impressive Muslim monuments in the Indian subcontinent."},
        {"name": "Mahasthangarh", "location": "Bogura", "type": "Heritage", "days_needed": 1, "cost_per_day": 1500, "travel_from_dhaka_cost": 1500, "travel_from_dhaka_hours": 5, "description": "One of the earliest urban archaeological sites in Bangladesh."},
        {"name": "Somapura Mahavihara", "location": "Naogaon", "type": "Heritage", "days_needed": 1, "cost_per_day": 1300, "travel_from_dhaka_cost": 1800, "travel_from_dhaka_hours": 6, "description": "Largest Buddhist vihara in the Indian subcontinent, a UNESCO World Heritage site."},
    ]
}

# Convert to DataFrame for easier querying
df_attractions = pd.DataFrame([item for sublist in attractions_data.values() for item in sublist])

def generate_tour_plan(budget, days, place_type, from_location):
    plan = {
        "status": "success",
        "message": "Here is your personalized tour plan for Bangladesh!",
        "parameters": {
            "budget": budget,
            "days": days,
            "place_type": place_type,
            "from_location": from_location
        },
        "itinerary": []
    }

    remaining_budget = budget
    remaining_days = days

    # Filter attractions by preferred type
    possible_attractions = df_attractions[df_attractions['type'] == place_type].copy()

    if possible_attractions.empty:
        plan["status"] = "error"
        plan["message"] = f"No attractions found for '{place_type}'. Please choose another type."
        return plan

    # Simple logic: Prioritize attractions based on 'travel_from_dhaka_cost'
    # If from_location is Dhaka, prefer nearby. If not Dhaka, this simple metric is less useful.
    if from_location == "Dhaka":
        possible_attractions['total_trip_cost_estimate'] = possible_attractions['cost_per_day'] * possible_attractions['days_needed'] + possible_attractions['travel_from_dhaka_cost']
        possible_attractions = possible_attractions.sort_values(by='total_trip_cost_estimate')
    else:
        # Fallback for other starting locations - simply sort by cost per day for now.
        # A real AI would calculate travel costs from 'from_location' to each attraction.
        possible_attractions['total_trip_cost_estimate'] = possible_attractions['cost_per_day'] * possible_attractions['days_needed']
        possible_attractions = possible_attractions.sort_values(by='total_trip_cost_estimate')


    selected_attractions = []
    current_day = 1 # Tracks the start day of the current segment

    # Loop to fill days and budget
    while remaining_days > 0 and not possible_attractions.empty:
        # Find attractions that fit remaining days and budget
        available_options = possible_attractions[
            (possible_attractions['days_needed'] <= remaining_days) &
            (possible_attractions['total_trip_cost_estimate'] <= remaining_budget)
        ]

        if available_options.empty:
            break # No more suitable attractions within constraints

        # Simple AI/Selection: Randomly pick one from available options
        # A more sophisticated AI would use recommendation algorithms here
        # (e.g., based on popularity, user ratings, sequential travel, etc.)
        selected_attraction = available_options.sample(1, random_state=random.seed()).iloc[0] # Use random.seed() for variety

        # Update remaining days and budget
        days_to_spend = selected_attraction['days_needed']
        cost_for_attraction = selected_attraction['total_trip_cost_estimate']

        remaining_days -= days_to_spend
        remaining_budget -= cost_for_attraction

        # Add to itinerary
        plan["itinerary"].append({
            "day": f"Day {current_day}",
            "end_day": f"Day {current_day + days_to_spend - 1}", # For multi-day attractions
            "location": selected_attraction['location'],
            "attraction": selected_attraction['name'],
            "description": selected_attraction['description'],
            "estimated_cost_for_this_segment": int(cost_for_attraction), # Ensure integer for display
            "days_spent_here": days_to_spend,
            "estimated_travel_hours_from_dhaka": selected_attraction['travel_from_dhaka_hours'],
            "note": f"Consider travel time and local transport from your starting location ({from_location})."
        })
        current_day += days_to_spend

        # Remove selected attraction to avoid re-selecting it (for this run)
        # For a more complex system, you might mark it as visited for the current plan.
        possible_attractions = possible_attractions.drop(selected_attraction.name)

    # Final check: If no itinerary was built, indicate an error
    if not plan["itinerary"]:
        plan["status"] = "error"
        plan["message"] = "Could not generate a suitable plan with the given constraints. Try adjusting your budget or the number of days."
    else:
        plan["remaining_budget"] = int(remaining_budget) # Ensure integer for display

    return plan

if __name__ == "__main__":
    # The PHP script will pass data as a single JSON string command-line argument.
    try:
        if len(sys.argv) < 2:
            raise IndexError("No input data provided.")

        input_data_str = sys.argv[1]
        input_data = json.loads(input_data_str)

        # Retrieve and validate inputs
        budget = int(input_data.get('budget'))
        days = int(input_data.get('days'))
        place_type = input_data.get('place_type')
        from_location = input_data.get('from_location')

        if not all([budget, days, place_type, from_location]):
            raise ValueError("Missing one or more required input parameters.")

        tour_plan = generate_tour_plan(budget, days, place_type, from_location)
        print(json.dumps(tour_plan)) # Output the generated plan as a JSON string to stdout

    except IndexError as e:
        print(json.dumps({"status": "error", "message": f"Input error: {str(e)} Please provide JSON data."}))
    except json.JSONDecodeError:
        print(json.dumps({"status": "error", "message": "Invalid JSON input received by Python script."}))
    except ValueError as e:
        print(json.dumps({"status": "error", "message": f"Input validation error: {str(e)} Ensure budget and days are numbers."}))
    except Exception as e:
        # Catch any other unexpected errors during execution
        print(json.dumps({"status": "error", "message": f"An unexpected error occurred in AI script: {str(e)}"}))