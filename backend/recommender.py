import pandas as pd

def load_data():
    file = 'AristoNature.xlsx'
    xl = pd.ExcelFile(file)
    
    location_types = ['Mountain', 'River', 'Forest', 'Sea', 'Heritage_']
    data = []
    for sheet in location_types:
        df = xl.parse(sheet)
        df['type'] = sheet.lower()
        data.append(df)
    
    combined = pd.concat(data, ignore_index=True)
    combined.columns = combined.columns.str.lower().str.strip()
    return combined

def recommend_plan(user_input):
    data = load_data()
    
    filtered = data[
        (data['estimated_cost'] <= user_input['budget']) &
        (data['estimated_days'] <= user_input['days']) &
        (data['type'].str.contains(user_input['preference'].lower())) &
        (data['food'].str.lower().str.contains(user_input['food'].lower()))
    ]
    
    if filtered.empty:
        return {"message": "No plans found matching your criteria."}
    
    return filtered.sort_values(by='rating', ascending=False).head(1).to_dict(orient='records')[0]
