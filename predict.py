#!/usr/bin/env python3
import sys
import joblib
import pandas as pd

# -----------------------------
# Load trained model
# -----------------------------
try:
    model = joblib.load("pet_disease_model.pkl")
except Exception as e:
    print(f"Error loading model: {e}")
    sys.exit(1)

# -----------------------------
# Required fields
# -----------------------------
fields = [
    "species","age_years","breed","is_neutered","weight_kg","is_vaccinated",
    "lethargy_level","appetite_status","drinking_status",
    "is_vomiting","is_diarrhea","has_blood_in_stool",
    "is_straining_to_urinate","urinating_more_frequently","has_blood_in_urine",
    "is_coughing","is_sneezing","has_nasal_discharge",
    "is_itching_scratching","has_hair_loss","is_lame_limping"
]

# -----------------------------
# Validate input
# -----------------------------
if len(sys.argv) != len(fields) + 1:
    print(f"Error: expected {len(fields)} arguments, got {len(sys.argv)-1}")
    sys.exit(1)

# -----------------------------
# Build input dict
# -----------------------------
data = {}
for i, field in enumerate(fields):
    value = sys.argv[i+1]
    if field in ["age_years","weight_kg"]:
        try:
            data[field] = float(value)
        except:
            data[field] = 0.0
    else:
        data[field] = value

df = pd.DataFrame([data])

# -----------------------------
# Predict disease
# -----------------------------
try:
    prediction = model.predict(df)[0]
    print(prediction)
except Exception as e:
    print(f"Prediction error: {e}")
