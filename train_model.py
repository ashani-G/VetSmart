import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.compose import ColumnTransformer
from sklearn.preprocessing import OneHotEncoder, StandardScaler
from sklearn.pipeline import Pipeline
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import classification_report, accuracy_score
import joblib

# ================================
# 1. Load dataset
# ================================
df = pd.read_csv("Pet_Diseases_Data.csv")

# Drop rows with missing values (you can also use imputation)
df = df.dropna()

# ================================
# 2. Target & Features
# ================================
y = df["diagnosis"]  # target
X = df.drop(columns=["diagnosis", "case_id"])  # drop target & ID

# ================================
# 3. Separate feature types
# ================================
numeric_features = ["age_years", "weight_kg"]
categorical_features = [col for col in X.columns if col not in numeric_features]

# ================================
# 4. Preprocessing Pipeline
# ================================
preprocess = ColumnTransformer(
    transformers=[
        ("num", StandardScaler(), numeric_features),
        ("cat", OneHotEncoder(handle_unknown="ignore"), categorical_features)
    ]
)

# ================================
# 5. Model Pipeline
# ================================
model = Pipeline(steps=[
    ("preprocess", preprocess),
    ("clf", LogisticRegression(max_iter=1000))
])

# ================================
# 6. Train/Test Split
# ================================
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42, stratify=y
)

# ================================
# 7. Train Model
# ================================
model.fit(X_train, y_train)

# ================================
# 8. Evaluate
# ================================
y_pred = model.predict(X_test)
print("Model Accuracy:", accuracy_score(y_test, y_pred))
print("\nClassification Report:\n", classification_report(y_test, y_pred))

# ================================
# 9. Save Model
# ================================
joblib.dump(model, "pet_disease_model.pkl")
print("Model saved as pet_disease_model.pkl")
