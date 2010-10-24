<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2010
 * 
 * ****************************************************************************************************
 * SACoche <http://sacoche.sesamath.net> - Suivi d'Acquisitions de Compétences
 * © Thomas Crespin pour Sésamath <http://www.sesamath.net> - Tous droits réservés.
 * Logiciel placé sous la licence libre GPL 3 <http://www.rodage.org/gpl-3.0.fr.html>.
 * ****************************************************************************************************
 * 
 * Ce fichier est une partie de SACoche.
 * 
 * SACoche est un logiciel libre ; vous pouvez le redistribuer ou le modifier suivant les termes 
 * de la “GNU General Public License” telle que publiée par la Free Software Foundation :
 * soit la version 3 de cette licence, soit (à votre gré) toute version ultérieure.
 * 
 * SACoche est distribué dans l’espoir qu’il vous sera utile, mais SANS AUCUNE GARANTIE :
 * sans même la garantie implicite de COMMERCIALISABILITÉ ni d’ADÉQUATION À UN OBJECTIF PARTICULIER.
 * Consultez la Licence Générale Publique GNU pour plus de détails.
 * 
 * Vous devriez avoir reçu une copie de la Licence Générale Publique GNU avec SACoche ;
 * si ce n’est pas le cas, consultez : <http://www.gnu.org/licenses/>.
 * 
 */

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
// Afficher un lien mailto en masquant l'adresse de courriel pour les robots
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

function mailto($email,$sujet,$affichage,$message='',$copy='')
{
	$mailto = 'mailto:'.$email.'?subject='.$sujet;
	$mailto.= ($copy) ? '&cc='.$copy : '' ;
	$mailto.= ($message) ? '&body='.$message : '' ;
	$tab_unicode_valeurs = utf8ToUnicode($mailto);
	$href = '&#'.implode(';'.'&#',$tab_unicode_valeurs).';';
	return '<a href="'.$href.'" class="lien_mail">'.$affichage.'</a>';
}

/**
 * Takes an UTF-8 string and returns an array of ints representing the Unicode characters.
 * Astral planes are supported ie. the ints in the output can be > 0xFFFF. Occurrances of the BOM are ignored. Surrogates are not allowed.
 * Returns false if the input string isn't a valid UTF-8 octet sequence.
 * 
 * Licence : NPL 1.1/GPL 2.0/LGPL 2.1
 * The Original Code is Mozilla Communicator client code.
 * The Initial Developer of the Original Code is Netscape Communications Corporation.
 * Contributor(s): Henri Sivonen, hsivonen@iki.fi
 * The latest version of this file can be obtained from http://iki.fi/hsivonen/php-utf8/
 * Version 1.0, 2003-05-30
 */

function utf8ToUnicode(&$str)
{
	$mState = 0;     // cached expected number of octets after the current octet until the beginning of the next UTF8 character sequence
	$mUcs4  = 0;     // cached Unicode character
	$mBytes = 1;     // cached expected number of octets in the current sequence
	$len = strlen($str);
	for($i = 0; $i < $len; $i++)
	{
		$in = ord($str{$i});
		if (0 == $mState)
		{
			// When mState is zero we expect either a US-ASCII character or a
			// multi-octet sequence.
			if (0 == (0x80 & ($in)))
			{
				// US-ASCII, pass straight through.
				$out[] = $in;
				$mBytes = 1;
			}
			else if (0xC0 == (0xE0 & ($in)))
			{
				// First octet of 2 octet sequence
				$mUcs4 = ($in);
				$mUcs4 = ($mUcs4 & 0x1F) << 6;
				$mState = 1;
				$mBytes = 2;
			}
			else if (0xE0 == (0xF0 & ($in))) 
			{
				// First octet of 3 octet sequence
				$mUcs4 = ($in);
				$mUcs4 = ($mUcs4 & 0x0F) << 12;
				$mState = 2;
				$mBytes = 3;
			}
			else if (0xF0 == (0xF8 & ($in)))
			{
				// First octet of 4 octet sequence
				$mUcs4 = ($in);
				$mUcs4 = ($mUcs4 & 0x07) << 18;
				$mState = 3;
				$mBytes = 4;
			}
			else if (0xF8 == (0xFC & ($in)))
			{
				/* First octet of 5 octet sequence.
				 *
				 * This is illegal because the encoded codepoint must be either
				 * (a) not the shortest form or
				 * (b) outside the Unicode range of 0-0x10FFFF.
				 * Rather than trying to resynchronize, we will carry on until the end
				 * of the sequence and let the later error handling code catch it.
				 */
				$mUcs4 = ($in);
				$mUcs4 = ($mUcs4 & 0x03) << 24;
				$mState = 4;
				$mBytes = 5;
			}
			else if (0xFC == (0xFE & ($in)))
			{
				// First octet of 6 octet sequence, see comments for 5 octet sequence.
				$mUcs4 = ($in);
				$mUcs4 = ($mUcs4 & 1) << 30;
				$mState = 5;
				$mBytes = 6;
			}
			else 
			{
				/* Current octet is neither in the US-ASCII range nor a legal first
				 * octet of a multi-octet sequence.
				 */
				return false;
			}
		}
		else 
		{
			// When mState is non-zero, we expect a continuation of the multi-octet
			// sequence
			if (0x80 == (0xC0 & ($in)))
			{
				// Legal continuation.
				$shift = ($mState - 1) * 6;
				$tmp = $in;
				$tmp = ($tmp & 0x0000003F) << $shift;
				$mUcs4 |= $tmp;
				if (0 == --$mState) 
				{
					/* End of the multi-octet sequence. mUcs4 now contains the final
					 * Unicode codepoint to be output
					 *
					 * Check for illegal sequences and codepoints.
					 */
					// From Unicode 3.1, non-shortest form is illegal
					if (((2 == $mBytes) && ($mUcs4 < 0x0080)) ||
							((3 == $mBytes) && ($mUcs4 < 0x0800)) ||
							((4 == $mBytes) && ($mUcs4 < 0x10000)) ||
							(4 < $mBytes) ||
							// From Unicode 3.2, surrogate characters are illegal
							(($mUcs4 & 0xFFFFF800) == 0xD800) ||
							// Codepoints outside the Unicode range are illegal
							($mUcs4 > 0x10FFFF))
					{
						return false;
					}
					if (0xFEFF != $mUcs4)
					{
						// BOM is legal but we don't want to output it
						$out[] = $mUcs4;
					}
					//initialize UTF8 cache
					$mState = 0;
					$mUcs4  = 0;
					$mBytes = 1;
				}
			} 
			else 
			{
				/* ((0xC0 & (*in) != 0x80) && (mState != 0))
				 * 
				 * Incomplete multi-octet sequence.
				 */
				return false;
			}
		}
	}
	return $out;
}

/**
 * Takes an array of ints representing the Unicode characters and returns a UTF-8 string.
 * Astral planes are supported ie. the ints in the input can be > 0xFFFF. Occurrances of the BOM are ignored. Surrogates are not allowed.
 * Returns false if the input array contains ints that represent surrogates or are outside the Unicode range.
 * 
 * Licence : NPL 1.1/GPL 2.0/LGPL 2.1
 * The Original Code is Mozilla Communicator client code.
 * The Initial Developer of the Original Code is Netscape Communications Corporation.
 * Contributor(s): Henri Sivonen, hsivonen@iki.fi
 * The latest version of this file can be obtained from http://iki.fi/hsivonen/php-utf8/
 * Version 1.0, 2003-05-30
 */

function unicodeToUtf8(&$arr)
{
	$dest = '';
	foreach ($arr as $src)
	{
		if($src < 0) 
		{
			return false;
		}
		else if ( $src <= 0x007f) 
		{
			$dest .= chr($src);
		}
		else if ($src <= 0x07ff) 
		{
			$dest .= chr(0xc0 | ($src >> 6));
			$dest .= chr(0x80 | ($src & 0x003f));
		} 
		else if($src == 0xFEFF) 
		{
			// nop -- zap the BOM
		}
		else if ($src >= 0xD800 && $src <= 0xDFFF) 
		{
			// found a surrogate
			return false;
		}
		else if ($src <= 0xffff) 
		{
			$dest .= chr(0xe0 | ($src >> 12));
			$dest .= chr(0x80 | (($src >> 6) & 0x003f));
			$dest .= chr(0x80 | ($src & 0x003f));
		}
		else if ($src <= 0x10ffff) 
		{
			$dest .= chr(0xf0 | ($src >> 18));
			$dest .= chr(0x80 | (($src >> 12) & 0x3f));
			$dest .= chr(0x80 | (($src >> 6) & 0x3f));
			$dest .= chr(0x80 | ($src & 0x3f));
		} else 
		{
			// out of range
			return false;
		}
	}
	return $dest;
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
// Passer d'une date MySQL AAAA-MM-JJ à une date française JJ/MM/AAAA et inversement
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

function convert_date_mysql_to_french($date)
{
	list($annee,$mois,$jour) = explode('-',$date);	// date_mysql de la forme aaaa-mm-jj
	return $jour.'/'.$mois.'/'.$annee;	// date_française de la forme jj/mm/aaaa
}

function convert_date_french_to_mysql($date)
{
	list($jour,$mois,$annee) = explode('/',$date);	// date_française de la forme jj/mm/aaaa
	return $annee.'-'.$mois.'-'.$jour;	// date_mysql de la forme aaaa-mm-jj
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
// Convertir une date MySQL en un texte bien formaté pour l'infobulle (sortie HTML)
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

function affich_date($date)
{
	if(mb_strpos($date,'-'))
	{
		list($annee,$mois,$jour) = explode('-',$date);	// date_mysql de la forme aaaa-mm-jj
	}
	else
	{
		list($jour,$mois,$annee) = explode('/',$date);	// date_française de la forme jj/mm/aaaa
	}

	$tab_mois = array('01'=>'janvier','02'=>'février','03'=>'mars','04'=>'avril','05'=>'mai','06'=>'juin','07'=>'juillet','08'=>'août','09'=>'septembre','10'=>'octobre','11'=>'novembre','12'=>'décembre');
	return $jour.' '.$tab_mois[$mois].' '.$annee;
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
// Afficher une note Lomer pour une sortie HTML
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

$tab_tri_note = array_flip(array('RR','R','V','VV','ABS','NN','DISP','REQ','-',''));	// sert pour le tri du tableau
function affich_note_html($note,$date,$info,$tri=false)
{
	global $tab_tri_note;
	$insert_tri = ($tri) ? '<i>'.$tab_tri_note[$note].'</i>' : '';
	$sous_dossier = (in_array($note,array('RR','R','V','VV'))) ? $_SESSION['NOTE_IMAGE_STYLE'].'/' : '';
	$title = ( ($date!='') || ($info!='') ) ? ' title="'.html($info).'<br />'.affich_date($date).'"' : '' ;
	return (in_array($note,array('REQ','-',''))) ? '&nbsp;' : $insert_tri.'<img'.$title.' alt="'.$note.'" src="./_img/note/'.$sous_dossier.$note.'.gif" />';
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
// Afficher un score bilan pour une sortie HTML
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

$tab_tri_etat = array_flip(array('r','o','v'));	// sert pour le tri du tableau dans le cas d'un tri par état d'acquisition
function affich_score_html($score,$methode_tri,$pourcent='')
{
	global $tab_tri_etat;
	// $methode_tri vaut 'score' ou 'etat'
	if($score===false)
	{
		$score_affiche = (strpos($_SESSION['DROIT_VOIR_SCORE_BILAN'],$_SESSION['USER_PROFIL'])!==false) ? '-' : '' ;
		return '<td class="hc">'.$score_affiche.'</td>';
	}
	elseif($score<$_SESSION['CALCUL_SEUIL']['R']) {$etat = 'r';}
	elseif($score>$_SESSION['CALCUL_SEUIL']['V']) {$etat = 'v';}
	else                                                   {$etat = 'o';}
	$score_affiche = (strpos($_SESSION['DROIT_VOIR_SCORE_BILAN'],$_SESSION['USER_PROFIL'])!==false) ? $score.$pourcent : '' ;
	$tri = ($methode_tri=='score') ? sprintf("%03u",$score) : $tab_tri_etat[$etat] ;	// le sprintf et le tab_tri_etat servent pour le tri du tableau
	return '<td class="hc '.$etat.'"><i>'.$tri.'</i>'.$score_affiche.'</td>';
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
// Afficher un pourcentage d'items acquis pour une sortie socle HTML
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

function affich_pourcentage_html($type_cellule,$tab_infos)
{
	// $tab_infos contient 'A' / 'VA' / 'NA' / 'nb' / '%'
	if($tab_infos['%']===false)
	{
		return '<'.$type_cellule.' class="hc">---</'.$type_cellule.'>';
	}
	elseif($tab_infos['%']<$_SESSION['CALCUL_SEUIL']['R']) {$etat = 'r';}
	elseif($tab_infos['%']>$_SESSION['CALCUL_SEUIL']['V']) {$etat = 'v';}
	else                                                   {$etat = 'o';}
	return '<'.$type_cellule.' class="hc '.$etat.'">'.$tab_infos['%'].'% acquis ('.$tab_infos['A'].'A '.$tab_infos['VA'].'VA '.$tab_infos['NA'].'NA)</'.$type_cellule.'>';
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
// Afficher un état de validation pour une sortie socle HTML
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

function affich_validation_html($type_cellule,$tab_infos)
{
	// $tab_infos contient 'etat' / 'date' / 'info'
	$etat  = ($tab_infos['etat']==1) ? 'Validé' : 'Invalidé' ;
	$texte = ($tab_infos['etat']==2) ? '---' : $tab_infos['date'] ;
	$bulle = ($tab_infos['etat']==2) ? '' : ' title="'.$etat.' le '.$tab_infos['date'].' par '.html($tab_infos['info']).'"' ;
	return '<'.$type_cellule.' class="hc v'.$tab_infos['etat'].'"'.$bulle.'>'.$texte.'</'.$type_cellule.'>';
}

?>