import pandas as pd
import numpy as np

# Étape 1 : Charger le dataset
# Chemin du fichier CSV que vous avez fourni
dataset_path = 'C:/Users/nourb/OneDrive/Documents/TEKUP/ING-3-J-G/semestre 2/IA/PROJET/diet_recommendations_dataset.csv'
try:
    df = pd.read_csv(dataset_path)
    print("Dataset chargé avec succès !")
except FileNotFoundError:
    print("Erreur : Fichier CSV non trouvé. Vérifiez le chemin.")
    exit()

# Étape 2 : Explorer le dataset
# Afficher les premières lignes pour vérifier le chargement
print("Aperçu des 5 premières lignes du dataset :")
print(df.head())

# Afficher les colonnes disponibles
print("\nColonnes du dataset :")
print(df.columns.tolist())

# Afficher des informations sur les types de données et les valeurs manquantes
print("\nInformations sur le dataset :")
print(df.info())

# Vérifier les valeurs uniques pour les colonnes catégoriques clés
print("\nValeurs uniques pour Disease_Type :")
print(df['Disease_Type'].value_counts())
print("\nValeurs uniques pour Diet_Recommendation :")
print(df['Diet_Recommendation'].value_counts())
print("\nValeurs uniques pour Dietary_Restrictions :")
print(df['Dietary_Restrictions'].value_counts())
print("\nValeurs uniques pour Allergies :")
print(df['Allergies'].value_counts())

# Étape 3 : Nettoyer le dataset
# Vérifier les valeurs manquantes
print("\nValeurs manquantes par colonne :")
print(df.isnull().sum())

# Supprimer les lignes avec des valeurs manquantes dans les colonnes critiques
critical_columns = ['Disease_Type', 'Diet_Recommendation', 'Dietary_Restrictions', 'Allergies', 'Preferred_Cuisine']
df = df.dropna(subset=critical_columns)

# Vérifier les doublons basés sur Patient_ID
print("\nNombre de doublons (basés sur Patient_ID) :")
print(df['Patient_ID'].duplicated().sum())

# Supprimer les doublons si nécessaire
df = df.drop_duplicates(subset=['Patient_ID'], keep='first')

# Vérifier les incohérences dans les colonnes numériques (ex. Age, BMI)
print("\nStatistiques descriptives pour les colonnes numériques :")
print(df[['Age', 'Weight_kg', 'Height_cm', 'BMI', 'Daily_Caloric_Intake']].describe())

# Corriger les valeurs aberrantes (ex. BMI négatif ou Age > 120)
df = df[(df['Age'] >= 18) & (df['Age'] <= 120)]  # Âge raisonnable
df = df[df['BMI'] > 0]  # BMI positif

# Étape 4 : Créer une colonne Description pour le RAG
# Combiner les colonnes pertinentes pour créer des descriptions textuelles
df['Description'] = df.apply(
    lambda x: f"Patient de {x['Age']} ans, {x['Gender']}, avec {x['Disease_Type']} (sévérité: {x['Severity']}). "
              f"Restrictions alimentaires: {x['Dietary_Restrictions']}. Allergies: {x['Allergies']}. "
              f"Cuisine préférée: {x['Preferred_Cuisine']}. Activité physique: {x['Physical_Activity_Level']}. "
              f"Recommandation alimentaire: {x['Diet_Recommendation']}.",
    axis=1
)

# Afficher un exemple de descriptions
print("\nExemple de descriptions générées :")
print(df['Description'].head())

# Étape 5 : Sauvegarder le dataset préparé
# Sauvegarder uniquement les colonnes nécessaires pour le RAG en CSV
output_columns = ['Patient_ID', 'Description', 'Disease_Type', 'Diet_Recommendation', 'Dietary_Restrictions', 'Allergies', 'Preferred_Cuisine']
df_output = df[output_columns]
csv_output_path = 'prepared_diet_dataset.csv'
df_output.to_csv(csv_output_path, index=False)
print(f"\nDataset préparé sauvegardé en CSV : {csv_output_path}")

# Étape 6 : Sauvegarder le dataset en Excel
# Installer openpyxl si nécessaire : `conda install openpyxl`
# Sauvegarder le dataset complet (toutes les colonnes) en Excel
excel_full_path = 'C:/Users/nourb/OneDrive/Documents/TEKUP/ING-3-J-G/semestre 2/IA/PROJET/diet_recommendations_full.xlsx'
df.to_excel(excel_full_path, index=False, engine='openpyxl')
print(f"Dataset complet sauvegardé en Excel : {excel_full_path}")

# Sauvegarder le dataset préparé (colonnes pour RAG) en Excel
excel_output_path = 'C:/Users/nourb/OneDrive/Documents/TEKUP/ING-3-J-G/semestre 2/IA/PROJET/prepared_diet_dataset.xlsx'
df_output.to_excel(excel_output_path, index=False, engine='openpyxl')
print(f"Dataset préparé pour RAG sauvegardé en Excel : {excel_output_path}")