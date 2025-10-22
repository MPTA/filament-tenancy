#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script to automatically detect and add attraction types to JSON files
Based on AttractionTypeEnum: natural, cultural, man_made, leisure, sport, events, uncategorized
"""

import json
import re
from pathlib import Path
from typing import Dict, List, Any

# ==================== TYPE DETECTION CONFIGURATION ====================

TYPE_KEYWORDS = {
    'natural': {
        'zh_CN': {
            # High priority - very specific natural features
            'high': ['山', '湖', '江', '河', '峰', '岛', '滩', '海', '峡谷', '森林', '瀑布', 
                     '温泉', '洞穴', '溶洞', '自然保护区', '湿地', '草原', '沙漠', '火山'],
            # Medium priority
            'medium': ['风景区', '景区', '自然', '生态'],
            # Low priority
            'low': ['公园']  # Parks can be natural or leisure
        },
        'en': {
            'high': ['mountain', 'lake', 'river', 'peak', 'island', 'beach', 'sea', 'ocean',
                     'valley', 'canyon', 'forest', 'waterfall', 'spring', 'cave', 'wetland',
                     'grassland', 'desert', 'volcano', 'nature reserve', 'scenic area'],
            'medium': ['natural', 'ecological', 'scenery', 'landscape'],
            'low': ['park']
        }
    },
    'cultural': {
        'zh_CN': {
            'high': ['博物馆', '寺', '庙', '塔', '古迹', '遗址', '故宫', '宫', '殿', 
                     '陵墓', '墓', '祠', '书院', '文庙', '碑林', '石窟', '古城', '古镇',
                     '文化遗产', '历史', '传统'],
            'medium': ['文化', '艺术', '纪念'],
            'low': ['馆']  # Hall/Museum - can be cultural or other
        },
        'en': {
            'high': ['museum', 'temple', 'palace', 'pagoda', 'heritage', 'historic', 
                     'ancient', 'tomb', 'mausoleum', 'monastery', 'shrine', 'archaeological',
                     'cultural site', 'memorial', 'traditional'],
            'medium': ['cultural', 'art', 'history'],
            'low': ['hall']
        }
    },
    'man_made': {
        'zh_CN': {
            'high': ['城墙', '长城', '城', '城市', '广场', '街道', '步行街', '商业街', 
                     '建筑', '大厦', '塔楼', '桥', '大桥', '隧道', '水坝', '纪念碑'],
            'medium': ['中心', '区域'],
            'low': []
        },
        'en': {
            'high': ['wall', 'great wall', 'city wall', 'fortress', 'city center', 'square',
                     'street', 'walking street', 'building', 'tower', 'bridge', 'tunnel',
                     'dam', 'monument', 'architecture'],
            'medium': ['urban', 'downtown', 'district'],
            'low': []
        }
    },
    'leisure': {
        'zh_CN': {
            'high': ['游乐园', '主题公园', '水族馆', '动物园', '植物园', '海洋公园', 
                     '欢乐谷', '迪士尼', '游乐场', '乐园', '水上乐园'],
            'medium': ['娱乐', '休闲', '度假村'],
            'low': ['公园']  # Generic park
        },
        'en': {
            'high': ['amusement park', 'theme park', 'aquarium', 'zoo', 'botanical garden',
                     'ocean park', 'disneyland', 'playground', 'water park', 'resort'],
            'medium': ['entertainment', 'leisure', 'recreation'],
            'low': ['park']
        }
    },
    'sport': {
        'zh_CN': {
            'high': ['滑雪场', '滑雪', '滑冰', '运动场', '体育场', '体育馆', '高尔夫',
                     '登山', '攀岩', '潜水', '冲浪', '跳伞'],
            'medium': ['运动', '体育', '健身'],
            'low': []
        },
        'en': {
            'high': ['ski resort', 'skiing', 'skating', 'stadium', 'sports center', 'golf',
                     'climbing', 'diving', 'surfing', 'skydiving', 'gym'],
            'medium': ['sport', 'sports', 'fitness', 'athletic'],
            'low': []
        }
    },
    'events': {
        'zh_CN': {
            'high': ['演出', '表演', '剧场', '剧院', '音乐厅', '歌剧院', '马戏', '秀场'],
            'medium': ['活动', '节庆', '庆典'],
            'low': []
        },
        'en': {
            'high': ['show', 'performance', 'theater', 'theatre', 'opera house', 
                     'concert hall', 'circus', 'live show'],
            'medium': ['event', 'festival', 'celebration'],
            'low': []
        }
    }
}

# Special patterns for combined checks
SPECIAL_PATTERNS = {
    'natural_park': {
        'patterns': [
            r'(国家|森林|自然|生态|风景)(公园|保护区)',
            r'(national|forest|nature|ecological|natural)\s*(park|reserve)'
        ],
        'type': 'natural'
    },
    'leisure_park': {
        'patterns': [
            r'(主题|游乐|欢乐|水上)(公园|乐园)',
            r'(theme|amusement|water)\s*park'
        ],
        'type': 'leisure'
    }
}


def calculate_type_score(text: str, keywords: Dict[str, List[str]], is_chinese: bool) -> int:
    """
    Calculate score for a specific type based on keyword matching
    
    Args:
        text: Text to analyze
        keywords: Dictionary with 'high', 'medium', 'low' priority keywords
        is_chinese: Whether text is Chinese
        
    Returns:
        Score (higher = better match)
    """
    if not text:
        return 0
    
    text_lower = text.lower()
    score = 0
    
    # High priority keywords: +10 points each
    for keyword in keywords.get('high', []):
        if is_chinese:
            if keyword in text:
                score += 10
        else:
            # English: use word boundaries
            if re.search(r'\b' + re.escape(keyword.lower()) + r'\b', text_lower):
                score += 10
    
    # Medium priority keywords: +5 points each
    for keyword in keywords.get('medium', []):
        if is_chinese:
            if keyword in text:
                score += 5
        else:
            if re.search(r'\b' + re.escape(keyword.lower()) + r'\b', text_lower):
                score += 5
    
    # Low priority keywords: +2 points each
    for keyword in keywords.get('low', []):
        if is_chinese:
            if keyword in text:
                score += 2
        else:
            if re.search(r'\b' + re.escape(keyword.lower()) + r'\b', text_lower):
                score += 2
    
    return score


def check_special_patterns(text: str) -> str | None:
    """Check for special combined patterns that override basic matching"""
    if not text:
        return None
    
    for pattern_name, config in SPECIAL_PATTERNS.items():
        for pattern in config['patterns']:
            if re.search(pattern, text, re.IGNORECASE):
                return config['type']
    
    return None


def detect_attraction_type(attraction: Dict[str, Any]) -> str:
    """
    Detect attraction type using intelligent keyword-based algorithm
    
    Priority order:
    1. Special patterns (e.g., "national park" → natural)
    2. Name matching (Chinese and English)
    3. Description matching (Chinese, then English if available)
    
    Args:
        attraction: Attraction data dictionary
        
    Returns:
        Type string matching AttractionTypeEnum
    """
    # Extract texts for analysis
    name_zh = attraction.get('name', {}).get('zh_CN', '')
    name_en = attraction.get('name', {}).get('en', '')
    
    desc_zh = ''
    desc_en = ''
    if isinstance(attraction.get('description'), dict):
        desc_zh = attraction['description'].get('zh_CN', '')
        desc_en = attraction['description'].get('en', '')
    
    # Step 1: Check special patterns in name first (highest priority)
    combined_name = f"{name_zh} {name_en}"
    special_type = check_special_patterns(combined_name)
    if special_type:
        return special_type
    
    # Step 2: Score each type based on name and description
    type_scores = {}
    
    for attraction_type, languages in TYPE_KEYWORDS.items():
        score = 0
        
        # Chinese name (weight: 3x - most reliable)
        score += calculate_type_score(name_zh, languages['zh_CN'], True) * 3
        
        # English name (weight: 2x)
        score += calculate_type_score(name_en, languages['en'], False) * 2
        
        # Chinese description (weight: 1x - less reliable, more generic)
        # Only check first 200 characters for efficiency
        desc_zh_short = desc_zh[:200] if desc_zh else ''
        score += calculate_type_score(desc_zh_short, languages['zh_CN'], True)
        
        # English description (weight: 0.5x - even less reliable)
        desc_en_short = desc_en[:200] if desc_en else ''
        score += calculate_type_score(desc_en_short, languages['en'], False) * 0.5
        
        type_scores[attraction_type] = score
    
    # Step 3: Determine best match
    max_score = max(type_scores.values())
    
    # If no clear winner (score < 5), mark as uncategorized
    if max_score < 5:
        return 'uncategorized'
    
    # Return type with highest score
    best_type = max(type_scores.items(), key=lambda x: x[1])[0]
    return best_type


def process_json_file(file_path: Path) -> Dict[str, Any]:
    """
    Process a single JSON file and add type field to each attraction
    
    Args:
        file_path: Path to JSON file
        
    Returns:
        Statistics dictionary
    """
    print(f"\n{'='*60}")
    print(f"Processing: {file_path.name}")
    print(f"{'='*60}")
    
    # Read JSON
    with open(file_path, 'r', encoding='utf-8') as f:
        attractions = json.load(f)
    
    # Statistics
    stats = {
        'total': len(attractions),
        'types': {}
    }
    
    # Process each attraction
    for attraction in attractions:
        # Detect type
        detected_type = detect_attraction_type(attraction)
        
        # Add type field (insert after name for readability)
        # We'll rebuild the dict with type in the right position
        new_attraction = {}
        for key, value in attraction.items():
            new_attraction[key] = value
            if key == 'name':
                new_attraction['type'] = detected_type
        
        # Update original attraction dict
        attraction.clear()
        attraction.update(new_attraction)
        
        # Update stats
        stats['types'][detected_type] = stats['types'].get(detected_type, 0) + 1
    
    # Save updated JSON
    with open(file_path, 'w', encoding='utf-8') as f:
        json.dump(attractions, f, ensure_ascii=False, indent=2)
    
    # Print statistics
    print(f"\n📊 Statistics:")
    print(f"   Total attractions: {stats['total']}")
    print(f"\n   Type distribution:")
    for type_name, count in sorted(stats['types'].items()):
        percentage = (count / stats['total']) * 100
        print(f"      • {type_name:15s}: {count:4d} ({percentage:5.1f}%)")
    
    return stats


def main():
    """Main execution function"""
    # Get all JSON files (exclude PROCESSING_PLAN.md and other non-attraction files)
    data_dir = Path(__file__).parent
    json_files = sorted([
        f for f in data_dir.glob('*.json')
        if f.stem not in ['package', 'package-lock']  # Exclude non-attraction files
    ])
    
    if not json_files:
        print("❌ No JSON files found!")
        return
    
    print("\n" + "="*60)
    print("🎯 ATTRACTION TYPE DETECTION SYSTEM")
    print("="*60)
    print(f"\nFound {len(json_files)} JSON files to process\n")
    
    # Process all files
    all_stats = {
        'files_processed': 0,
        'total_attractions': 0,
        'global_types': {}
    }
    
    for json_file in json_files:
        try:
            stats = process_json_file(json_file)
            all_stats['files_processed'] += 1
            all_stats['total_attractions'] += stats['total']
            
            # Aggregate global stats
            for type_name, count in stats['types'].items():
                all_stats['global_types'][type_name] = \
                    all_stats['global_types'].get(type_name, 0) + count
                    
        except Exception as e:
            print(f"❌ Error processing {json_file.name}: {e}")
            continue
    
    # Print global summary
    print("\n" + "="*60)
    print("📈 GLOBAL SUMMARY")
    print("="*60)
    print(f"\n✅ Files processed: {all_stats['files_processed']}")
    print(f"✅ Total attractions: {all_stats['total_attractions']}")
    print(f"\n📊 Global type distribution:")
    
    for type_name, count in sorted(all_stats['global_types'].items(), 
                                   key=lambda x: x[1], reverse=True):
        percentage = (count / all_stats['total_attractions']) * 100
        bar_length = int(percentage / 2)  # Scale for display
        bar = "█" * bar_length
        print(f"   {type_name:15s}: {count:5d} ({percentage:5.1f}%) {bar}")
    
    print("\n" + "="*60)
    print("✅ Processing complete!")
    print("="*60 + "\n")


if __name__ == '__main__':
    main()

