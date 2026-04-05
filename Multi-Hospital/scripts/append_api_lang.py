# Append prescription API error strings after quick_view_load_failed if missing.
import os
import re

INSERT = """
$lang['prescription_id_required'] = 'No prescription ID provided.';
$lang['prescription_not_found'] = 'Prescription not found.';
$lang['permission_denied'] = 'Permission denied.';
"""

# Localized bundles (language folder name -> insert block without leading newline issues)
LOCAL = {
    "french": """
$lang['prescription_id_required'] = 'Aucun identifiant d’ordonnance fourni.';
$lang['prescription_not_found'] = 'Ordonnance introuvable.';
$lang['permission_denied'] = 'Permission refusée.';
""",
    "spanish": """
$lang['prescription_id_required'] = 'No se proporcionó el ID de la receta.';
$lang['prescription_not_found'] = 'Receta no encontrada.';
$lang['permission_denied'] = 'Permiso denegado.';
""",
    "german": """
$lang['prescription_id_required'] = 'Keine Rezept-ID angegeben.';
$lang['prescription_not_found'] = 'Rezept nicht gefunden.';
$lang['permission_denied'] = 'Zugriff verweigert.';
""",
    "arabic": """
$lang['prescription_id_required'] = 'لم يُرسل رقم الوصفة.';
$lang['prescription_not_found'] = 'الوصفة غير موجودة.';
$lang['permission_denied'] = 'تم رفض الإذن.';
""",
    "zh_cn": """
$lang['prescription_id_required'] = '未提供处方编号。';
$lang['prescription_not_found'] = '未找到处方。';
$lang['permission_denied'] = '无权限。';
""",
    "zh_tw": """
$lang['prescription_id_required'] = '未提供處方編號。';
$lang['prescription_not_found'] = '找不到處方。';
$lang['permission_denied'] = '無權限。';
""",
    "portuguese": """
$lang['prescription_id_required'] = 'Nenhum ID de receita fornecido.';
$lang['prescription_not_found'] = 'Receita não encontrada.';
$lang['permission_denied'] = 'Permissão negada.';
""",
    "italian": """
$lang['prescription_id_required'] = 'ID ricetta non fornito.';
$lang['prescription_not_found'] = 'Ricetta non trovata.';
$lang['permission_denied'] = 'Permesso negato.';
""",
    "turkish": """
$lang['prescription_id_required'] = 'Reçete kimliği sağlanmadı.';
$lang['prescription_not_found'] = 'Reçete bulunamadı.';
$lang['permission_denied'] = 'İzin reddedildi.';
""",
    "russian": """
$lang['prescription_id_required'] = 'Не указан ID рецепта.';
$lang['prescription_not_found'] = 'Рецепт не найден.';
$lang['permission_denied'] = 'Доступ запрещён.';
""",
    "japanese": """
$lang['prescription_id_required'] = '処方IDがありません。';
$lang['prescription_not_found'] = '処方が見つかりません。';
$lang['permission_denied'] = '権限がありません。';
""",
    "korean": """
$lang['prescription_id_required'] = '처방 ID가 없습니다.';
$lang['prescription_not_found'] = '처방을 찾을 수 없습니다.';
$lang['permission_denied'] = '권한이 거부되었습니다.';
""",
    "polish": """
$lang['prescription_id_required'] = 'Nie podano ID recepty.';
$lang['prescription_not_found'] = 'Nie znaleziono recepty.';
$lang['permission_denied'] = 'Brak uprawnień.';
""",
    "dutch": """
$lang['prescription_id_required'] = 'Geen recept-ID opgegeven.';
$lang['prescription_not_found'] = 'Recept niet gevonden.';
$lang['permission_denied'] = 'Toegang geweigerd.';
""",
    "persian": """
$lang['prescription_id_required'] = 'شناسه نسخه ارسال نشده است.';
$lang['prescription_not_found'] = 'نسخه یافت نشد.';
$lang['permission_denied'] = 'دسترسی رد شد.';
""",
    "vietnamese": """
$lang['prescription_id_required'] = 'Chưa cung cấp mã đơn thuốc.';
$lang['prescription_not_found'] = 'Không tìm thấy đơn thuốc.';
$lang['permission_denied'] = 'Không có quyền.';
""",
}

base = os.path.join(os.path.dirname(__file__), "..", "application", "language")
for name in sorted(os.listdir(base)):
    d = os.path.join(base, name)
    p = os.path.join(d, "system_syntax_lang.php")
    if not os.path.isfile(p):
        continue
    t = open(p, encoding="utf-8", errors="replace").read()
    if "prescription_id_required" in t:
        continue
    if "quick_view_load_failed" not in t:
        continue
    block = LOCAL.get(name, INSERT)
    t = re.sub(
        r"(\$lang\['quick_view_load_failed'\]\s*=\s*'[^']*';)\s*",
        r"\1\n" + block.strip() + "\n",
        t,
        count=1,
    )
    if "prescription_id_required" not in t:
        print("SKIP (no match):", name)
        continue
    open(p, "w", encoding="utf-8", newline="\n").write(t)
    print(name)
