- -   M y S Q L   d u m p   1 0 . 1 3     D i s t r i b   5 . 6 . 1 7 ,   f o r   W i n 6 4   ( x 8 6 _ 6 4 )  
 - -  
 - -   H o s t :   l o c a l h o s t         D a t a b a s e :   O l a R e n t  
 - -   - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  
 - -   S e r v e r   v e r s i o n 	 5 . 6 . 1 7  
  
 / * ! 4 0 1 0 1   S E T   @ O L D _ C H A R A C T E R _ S E T _ C L I E N T = @ @ C H A R A C T E R _ S E T _ C L I E N T   * / ;  
 / * ! 4 0 1 0 1   S E T   @ O L D _ C H A R A C T E R _ S E T _ R E S U L T S = @ @ C H A R A C T E R _ S E T _ R E S U L T S   * / ;  
 / * ! 4 0 1 0 1   S E T   @ O L D _ C O L L A T I O N _ C O N N E C T I O N = @ @ C O L L A T I O N _ C O N N E C T I O N   * / ;  
 / * ! 4 0 1 0 1   S E T   N A M E S   u t f 8   * / ;  
 / * ! 4 0 1 0 3   S E T   @ O L D _ T I M E _ Z O N E = @ @ T I M E _ Z O N E   * / ;  
 / * ! 4 0 1 0 3   S E T   T I M E _ Z O N E = ' + 0 0 : 0 0 '   * / ;  
 / * ! 4 0 0 1 4   S E T   @ O L D _ U N I Q U E _ C H E C K S = @ @ U N I Q U E _ C H E C K S ,   U N I Q U E _ C H E C K S = 0   * / ;  
 / * ! 4 0 0 1 4   S E T   @ O L D _ F O R E I G N _ K E Y _ C H E C K S = @ @ F O R E I G N _ K E Y _ C H E C K S ,   F O R E I G N _ K E Y _ C H E C K S = 0   * / ;  
 / * ! 4 0 1 0 1   S E T   @ O L D _ S Q L _ M O D E = @ @ S Q L _ M O D E ,   S Q L _ M O D E = ' N O _ A U T O _ V A L U E _ O N _ Z E R O '   * / ;  
 / * ! 4 0 1 1 1   S E T   @ O L D _ S Q L _ N O T E S = @ @ S Q L _ N O T E S ,   S Q L _ N O T E S = 0   * / ;  
  
 - -  
 - -   T a b l e   s t r u c t u r e   f o r   t a b l e   ` F i l e `  
 - -  
  
 D R O P   T A B L E   I F   E X I S T S   ` F i l e ` ;  
 / * ! 4 0 1 0 1   S E T   @ s a v e d _ c s _ c l i e n t           =   @ @ c h a r a c t e r _ s e t _ c l i e n t   * / ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   u t f 8   * / ;  
 C R E A T E   T A B L E   ` F i l e `   (  
     ` i d `   i n t ( 1 1 )   N O T   N U L L   A U T O _ I N C R E M E N T ,  
     ` f i l e N a m e `   v a r c h a r ( 3 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   N O T   N U L L ,  
     ` c o m m e n t `   v a r c h a r ( 6 4 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` t y p e `   t i n y i n t ( 4 )   N O T   N U L L   C O M M E N T   ' 1   -   T a x   f i l e ,   2   -   I n c o m e   p r o o f ,   3   -   B a n k   G u a r a n t e e ,   4   -   C a u t i o n   S o l i d a r e ,   5   -   C o - S i g n e r ,   6   -   I D ' ,  
     ` u s e r I d `   i n t ( 1 1 )   N O T   N U L L ,  
     P R I M A R Y   K E Y   ( ` i d ` ) ,  
     K E Y   ` u s e r I d `   ( ` u s e r I d ` ) ,  
     C O N S T R A I N T   ` f i l e _ i b f k _ 1 `   F O R E I G N   K E Y   ( ` u s e r I d ` )   R E F E R E N C E S   ` U s e r `   ( ` i d ` )  
 )   E N G I N E = I n n o D B   A U T O _ I N C R E M E N T = 3   D E F A U L T   C H A R S E T = u t f 8   C O L L A T E = u t f 8 _ u n i c o d e _ c i ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   @ s a v e d _ c s _ c l i e n t   * / ;  
  
 - -  
 - -   T a b l e   s t r u c t u r e   f o r   t a b l e   ` N o t i f i c a t i o n `  
 - -  
  
 D R O P   T A B L E   I F   E X I S T S   ` N o t i f i c a t i o n ` ;  
 / * ! 4 0 1 0 1   S E T   @ s a v e d _ c s _ c l i e n t           =   @ @ c h a r a c t e r _ s e t _ c l i e n t   * / ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   u t f 8   * / ;  
 C R E A T E   T A B L E   ` N o t i f i c a t i o n `   (  
     ` i d `   i n t ( 1 1 )   N O T   N U L L   A U T O _ I N C R E M E N T ,  
     ` u s e r I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` v i e w S t a t u s `   t i n y i n t ( 1 )   D E F A U L T   N U L L   C O M M E N T   ' N o t i f i c a t i o n   v i e w   s t a t u s .   0   -   n o t   v i e w e d ,   1   -   v i e w e d ' ,  
     ` m e s s a g e C o d e `   v a r c h a r ( 2 5 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   N O T   N U L L ,  
     ` p a r a m s `   t i n y t e x t   C O L L A T E   u t f 8 _ u n i c o d e _ c i   N O T   N U L L   C O M M E N T   ' D y a n m i c   p a r a m e t e r s   t o   b e   a p p e n d e d   t o   m e s s a g e ' ,  
     ` c r e a t e d A t `   d a t e t i m e   N O T   N U L L ,  
     P R I M A R Y   K E Y   ( ` i d ` ) ,  
     K E Y   ` f k _ N o t i f i c a t i o n _ U s e r 1 _ i d x `   ( ` u s e r I d ` ) ,  
     C O N S T R A I N T   ` f k _ N o t i f i c a t i o n _ U s e r 1 `   F O R E I G N   K E Y   ( ` u s e r I d ` )   R E F E R E N C E S   ` U s e r `   ( ` i d ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N  
 )   E N G I N E = I n n o D B   A U T O _ I N C R E M E N T = 3 2   D E F A U L T   C H A R S E T = u t f 8   C O L L A T E = u t f 8 _ u n i c o d e _ c i ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   @ s a v e d _ c s _ c l i e n t   * / ;  
  
 - -  
 - -   T a b l e   s t r u c t u r e   f o r   t a b l e   ` N o t i f i c a t i o n Q u e u e `  
 - -  
  
 D R O P   T A B L E   I F   E X I S T S   ` N o t i f i c a t i o n Q u e u e ` ;  
 / * ! 4 0 1 0 1   S E T   @ s a v e d _ c s _ c l i e n t           =   @ @ c h a r a c t e r _ s e t _ c l i e n t   * / ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   u t f 8   * / ;  
 C R E A T E   T A B L E   ` N o t i f i c a t i o n Q u e u e `   (  
     ` i d `   i n t ( 1 1 )   N O T   N U L L   A U T O _ I N C R E M E N T ,  
     ` t y p e `   t i n y i n t ( 2 )   N O T   N U L L   C O M M E N T   ' 1   -   P r o p e r t y   A s s i g n   A n o t h e r ' ,  
     ` s t a t u s `   t i n y i n t ( 1 )   N O T   N U L L   D E F A U L T   ' 0 '   C O M M E N T   ' 0   -   p e n d i n g ,   1   -   i n   p r o g r e s s ,   2   -   c o m p l e t e d ' ,  
     ` d a t a `   t e x t   C O L L A T E   u t f 8 _ u n i c o d e _ c i   N O T   N U L L   C O M M E N T   ' J S O N   s t r i n g ' ,  
     ` c r e a t e d A t `   d a t e t i m e   N O T   N U L L ,  
     P R I M A R Y   K E Y   ( ` i d ` )  
 )   E N G I N E = I n n o D B   A U T O _ I N C R E M E N T = 3   D E F A U L T   C H A R S E T = u t f 8   C O L L A T E = u t f 8 _ u n i c o d e _ c i ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   @ s a v e d _ c s _ c l i e n t   * / ;  
  
 - -  
 - -   T a b l e   s t r u c t u r e   f o r   t a b l e   ` P a y m e n t `  
 - -  
  
 D R O P   T A B L E   I F   E X I S T S   ` P a y m e n t ` ;  
 / * ! 4 0 1 0 1   S E T   @ s a v e d _ c s _ c l i e n t           =   @ @ c h a r a c t e r _ s e t _ c l i e n t   * / ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   u t f 8   * / ;  
 C R E A T E   T A B L E   ` P a y m e n t `   (  
     ` i d `   i n t ( 1 1 )   N O T   N U L L   A U T O _ I N C R E M E N T ,  
     ` t e n a n t U s e r I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` p r o p e r t y I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` a m o u n t `   f l o a t   D E F A U L T   N U L L   C O M M E N T   ' C h a r g e d   a m o u n t ' ,  
     ` c r e a t e d A t `   d a t e t i m e   D E F A U L T   N U L L ,  
     P R I M A R Y   K E Y   ( ` i d ` ) ,  
     K E Y   ` f k _ P a y m e n t _ U s e r 1 _ i d x `   ( ` t e n a n t U s e r I d ` ) ,  
     C O N S T R A I N T   ` f k _ P a y m e n t _ U s e r 1 `   F O R E I G N   K E Y   ( ` t e n a n t U s e r I d ` )   R E F E R E N C E S   ` U s e r `   ( ` i d ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N  
 )   E N G I N E = I n n o D B   D E F A U L T   C H A R S E T = u t f 8   C O L L A T E = u t f 8 _ u n i c o d e _ c i ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   @ s a v e d _ c s _ c l i e n t   * / ;  
  
 - -  
 - -   T a b l e   s t r u c t u r e   f o r   t a b l e   ` P a y m e n t C a r d `  
 - -  
  
 D R O P   T A B L E   I F   E X I S T S   ` P a y m e n t C a r d ` ;  
 / * ! 4 0 1 0 1   S E T   @ s a v e d _ c s _ c l i e n t           =   @ @ c h a r a c t e r _ s e t _ c l i e n t   * / ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   u t f 8   * / ;  
 C R E A T E   T A B L E   ` P a y m e n t C a r d `   (  
     ` i d `   i n t ( 1 1 )   N O T   N U L L   A U T O _ I N C R E M E N T ,  
     ` u s e r I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` t o k e n `   v a r c h a r ( 4 5 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   N O T   N U L L ,  
     ` e x p i r e `   d a t e   D E F A U L T   N U L L ,  
     P R I M A R Y   K E Y   ( ` i d ` ) ,  
     K E Y   ` f k _ P a y m e n t C a r d s _ U s e r _ i d x `   ( ` u s e r I d ` ) ,  
     C O N S T R A I N T   ` f k _ P a y m e n t C a r d s _ U s e r `   F O R E I G N   K E Y   ( ` u s e r I d ` )   R E F E R E N C E S   ` U s e r `   ( ` i d ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N  
 )   E N G I N E = I n n o D B   D E F A U L T   C H A R S E T = u t f 8   C O L L A T E = u t f 8 _ u n i c o d e _ c i ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   @ s a v e d _ c s _ c l i e n t   * / ;  
  
 - -  
 - -   T a b l e   s t r u c t u r e   f o r   t a b l e   ` P e r m i s s i o n `  
 - -  
  
 D R O P   T A B L E   I F   E X I S T S   ` P e r m i s s i o n ` ;  
 / * ! 4 0 1 0 1   S E T   @ s a v e d _ c s _ c l i e n t           =   @ @ c h a r a c t e r _ s e t _ c l i e n t   * / ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   u t f 8   * / ;  
 C R E A T E   T A B L E   ` P e r m i s s i o n `   (  
     ` n a m e `   v a r c h a r ( 3 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   N O T   N U L L   D E F A U L T   ' ' ,  
     ` d e s c r i p t i o n `   v a r c h a r ( 6 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   N O T   N U L L ,  
     ` c a t e g o r y `   v a r c h a r ( 3 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   N O T   N U L L ,  
     ` c r e a t e d A t `   d a t e t i m e   N O T   N U L L ,  
     ` u p d a t e d A t `   d a t e t i m e   D E F A U L T   N U L L ,  
     ` c r e a t e d B y I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` u p d a t e d B y I d `   i n t ( 1 1 )   D E F A U L T   N U L L ,  
     P R I M A R Y   K E Y   ( ` n a m e ` )  
 )   E N G I N E = I n n o D B   D E F A U L T   C H A R S E T = u t f 8   C O L L A T E = u t f 8 _ u n i c o d e _ c i ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   @ s a v e d _ c s _ c l i e n t   * / ;  
  
 - -  
 - -   T a b l e   s t r u c t u r e   f o r   t a b l e   ` P r o p e r t y `  
 - -  
  
 D R O P   T A B L E   I F   E X I S T S   ` P r o p e r t y ` ;  
 / * ! 4 0 1 0 1   S E T   @ s a v e d _ c s _ c l i e n t           =   @ @ c h a r a c t e r _ s e t _ c l i e n t   * / ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   u t f 8   * / ;  
 C R E A T E   T A B L E   ` P r o p e r t y `   (  
     ` i d `   i n t ( 1 1 )   N O T   N U L L   A U T O _ I N C R E M E N T ,  
     ` o w n e r U s e r I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` t e n a n t U s e r I d `   i n t ( 1 1 )   D E F A U L T   N U L L ,  
     ` c o d e `   v a r c h a r ( 1 1 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   N O T   N U L L   C O M M E N T   ' C o d e   t o   s h a r e   w i t h   t e n a n t s ' ,  
     ` n a m e `   v a r c h a r ( 3 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   N O T   N U L L ,  
     ` d e s c r i p t i o n `   v a r c h a r ( 6 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` a d d r e s s `   v a r c h a r ( 9 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` g e o L o c a t i o n `   v a r c h a r ( 3 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L   C O M M E N T   ' G e o   c o o r d i n a t e s ' ,  
     ` c o s t `   f l o a t   D E F A U L T   N U L L ,  
     ` s t a t u s `   t i n y i n t ( 1 )   D E F A U L T   N U L L   C O M M E N T   ' 1   -   A v a i l a b l e ,   2   -   N o t   a v a i l a b l e ' ,  
     ` i m a g e N a m e `   v a r c h a r ( 3 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` c u r r e n t R e n t D u e A t `   d a t e   D E F A U L T   N U L L ,  
     ` p a y m e n t S t a t u s `   t i n y i n t ( 1 )   D E F A U L T   N U L L   C O M M E N T   ' C u r r e n t   c h a r g i n g   s t a t u s .   0   -   d e f a u l t ,   1   -   S u c c e s s ,   2   -   f a i l e d ' ,  
     ` z i p C o d e `   v a r c h a r ( 1 2 )   C H A R A C T E R   S E T   l a t i n 1   D E F A U L T   N U L L ,  
     ` n o O f R o o m s `   i n t ( 2 )   D E F A U L T   N U L L ,  
     ` s i z e `   i n t ( 4 )   D E F A U L T   N U L L   C O M M E N T   ' s i z e   i n   s q u a r e   f e e t ' ,  
     ` c r e a t e d A t `   d a t e t i m e   N O T   N U L L ,  
     ` u p d a t e d A t `   d a t e t i m e   D E F A U L T   N U L L ,  
     ` c r e a t e d B y I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` u p d a t e d B y I d `   i n t ( 1 1 )   D E F A U L T   N U L L ,  
     ` c h a r g i n g S t a r t D a t e `   d a t e   D E F A U L T   N U L L ,  
     ` n e x t C h a r g i n g D a t e `   d a t e   D E F A U L T   N U L L ,  
     ` c h a r g i n g C y c l e `   t i n y i n t ( 1 )   D E F A U L T   N U L L   C O M M E N T   ' 1   -   m o n t h l y ' ,  
     P R I M A R Y   K E Y   ( ` i d ` ) ,  
     K E Y   ` f k _ P r o p e r t y _ U s e r 1 _ i d x `   ( ` o w n e r U s e r I d ` ) ,  
     K E Y   ` f k _ P r o p e r t y _ U s e r 2 _ i d x `   ( ` t e n a n t U s e r I d ` ) ,  
     C O N S T R A I N T   ` f k _ P r o p e r t y _ U s e r 1 `   F O R E I G N   K E Y   ( ` o w n e r U s e r I d ` )   R E F E R E N C E S   ` U s e r `   ( ` i d ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N ,  
     C O N S T R A I N T   ` f k _ P r o p e r t y _ U s e r 2 `   F O R E I G N   K E Y   ( ` t e n a n t U s e r I d ` )   R E F E R E N C E S   ` U s e r `   ( ` i d ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N  
 )   E N G I N E = I n n o D B   A U T O _ I N C R E M E N T = 1 5   D E F A U L T   C H A R S E T = u t f 8   C O L L A T E = u t f 8 _ u n i c o d e _ c i ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   @ s a v e d _ c s _ c l i e n t   * / ;  
  
 - -  
 - -   T a b l e   s t r u c t u r e   f o r   t a b l e   ` P r o p e r t y H i s t o r y `  
 - -  
  
 D R O P   T A B L E   I F   E X I S T S   ` P r o p e r t y H i s t o r y ` ;  
 / * ! 4 0 1 0 1   S E T   @ s a v e d _ c s _ c l i e n t           =   @ @ c h a r a c t e r _ s e t _ c l i e n t   * / ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   u t f 8   * / ;  
 C R E A T E   T A B L E   ` P r o p e r t y H i s t o r y `   (  
     ` i d `   i n t ( 1 1 )   N O T   N U L L   A U T O _ I N C R E M E N T ,  
     ` t e n a n t U s e r I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` o w n e r U s e r I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` p r o p e r t y I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` f r o m D a t e `   d a t e   N O T   N U L L ,  
     ` t o D a t e `   d a t e   D E F A U L T   N U L L ,  
     P R I M A R Y   K E Y   ( ` i d ` ) ,  
     K E Y   ` f k _ T e n e n t P r o p e r t y H i s t o r y _ U s e r 1 _ i d x `   ( ` t e n a n t U s e r I d ` ) ,  
     K E Y   ` f k _ T e n e n t P r o p e r t y H i s t o r y _ U s e r 2 _ i d x `   ( ` o w n e r U s e r I d ` ) ,  
     K E Y   ` f k _ T e n e n t P r o p e r t y H i s t o r y _ P r o p e r t y 1 _ i d x `   ( ` p r o p e r t y I d ` ) ,  
     C O N S T R A I N T   ` f k _ T e n e n t P r o p e r t y H i s t o r y _ P r o p e r t y 1 `   F O R E I G N   K E Y   ( ` p r o p e r t y I d ` )   R E F E R E N C E S   ` P r o p e r t y `   ( ` i d ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N ,  
     C O N S T R A I N T   ` f k _ T e n e n t P r o p e r t y H i s t o r y _ U s e r 1 `   F O R E I G N   K E Y   ( ` t e n a n t U s e r I d ` )   R E F E R E N C E S   ` U s e r `   ( ` i d ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N ,  
     C O N S T R A I N T   ` f k _ T e n e n t P r o p e r t y H i s t o r y _ U s e r 2 `   F O R E I G N   K E Y   ( ` o w n e r U s e r I d ` )   R E F E R E N C E S   ` U s e r `   ( ` i d ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N  
 )   E N G I N E = I n n o D B   A U T O _ I N C R E M E N T = 2   D E F A U L T   C H A R S E T = u t f 8   C O L L A T E = u t f 8 _ u n i c o d e _ c i ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   @ s a v e d _ c s _ c l i e n t   * / ;  
  
 - -  
 - -   T a b l e   s t r u c t u r e   f o r   t a b l e   ` P r o p e r t y R e q u e s t `  
 - -  
  
 D R O P   T A B L E   I F   E X I S T S   ` P r o p e r t y R e q u e s t ` ;  
 / * ! 4 0 1 0 1   S E T   @ s a v e d _ c s _ c l i e n t           =   @ @ c h a r a c t e r _ s e t _ c l i e n t   * / ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   u t f 8   * / ;  
 C R E A T E   T A B L E   ` P r o p e r t y R e q u e s t `   (  
     ` i d `   i n t ( 1 1 )   N O T   N U L L   A U T O _ I N C R E M E N T ,  
     ` p r o p e r t y I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` c o d e `   v a r c h a r ( 1 1 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   N O T   N U L L ,  
     ` t e n a n t U s e r I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` o w n e r U s e r I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` s t a t u s `   t i n y i n t ( 1 )   N O T   N U L L   D E F A U L T   ' 0 '   C O M M E N T   ' 0   -   p e n d i n g ,   1   -   a c c e p t e d ,   2   -   r e j e c t e d ' ,  
     ` c r e a t e d A t `   d a t e t i m e   D E F A U L T   N U L L ,  
     ` p a y D a y `   i n t ( 1 1 )   D E F A U L T   N U L L ,  
     ` b o o k i n g D u r a t i o n `   i n t ( 1 1 )   D E F A U L T   N U L L ,  
     P R I M A R Y   K E Y   ( ` i d ` ) ,  
     K E Y   ` f k _ P r o p e r t y R e q u e s t _ P r o p e r t y 1 _ i d x `   ( ` p r o p e r t y I d ` ) ,  
     K E Y   ` f k _ P r o p e r t y R e q u e s t _ U s e r 1 _ i d x `   ( ` t e n a n t U s e r I d ` ) ,  
     C O N S T R A I N T   ` f k _ P r o p e r t y R e q u e s t _ P r o p e r t y 1 `   F O R E I G N   K E Y   ( ` p r o p e r t y I d ` )   R E F E R E N C E S   ` P r o p e r t y `   ( ` i d ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N ,  
     C O N S T R A I N T   ` f k _ P r o p e r t y R e q u e s t _ U s e r 1 `   F O R E I G N   K E Y   ( ` t e n a n t U s e r I d ` )   R E F E R E N C E S   ` U s e r `   ( ` i d ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N  
 )   E N G I N E = I n n o D B   A U T O _ I N C R E M E N T = 1 4   D E F A U L T   C H A R S E T = u t f 8   C O L L A T E = u t f 8 _ u n i c o d e _ c i ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   @ s a v e d _ c s _ c l i e n t   * / ;  
  
 - -  
 - -   T a b l e   s t r u c t u r e   f o r   t a b l e   ` R e v i e w R e q u e s t `  
 - -  
  
 D R O P   T A B L E   I F   E X I S T S   ` R e v i e w R e q u e s t ` ;  
 / * ! 4 0 1 0 1   S E T   @ s a v e d _ c s _ c l i e n t           =   @ @ c h a r a c t e r _ s e t _ c l i e n t   * / ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   u t f 8   * / ;  
 C R E A T E   T A B L E   ` R e v i e w R e q u e s t `   (  
     ` i d `   i n t ( 1 1 )   N O T   N U L L   A U T O _ I N C R E M E N T ,  
     ` r e q u e s t e r U s e r I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` r e c e i v e r U s e r I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` c r e a t e d A t `   d a t e t i m e   N O T   N U L L ,  
     ` s t a t u s `   t i n y i n t ( 2 )   N O T   N U L L   C O M M E N T   ' 0   -   P e n d i n g ,   1   -   r e v i e w e d ' ,  
     P R I M A R Y   K E Y   ( ` i d ` ) ,  
     K E Y   ` f k _ R e v i e w R e q u e s t _ U s e r 1 _ i d x `   ( ` r e q u e s t e r U s e r I d ` ) ,  
     K E Y   ` f k _ R e v i e w R e q u e s t _ U s e r 2 _ i d x `   ( ` r e c e i v e r U s e r I d ` ) ,  
     C O N S T R A I N T   ` f k _ R e v i e w R e q u e s t _ U s e r 1 `   F O R E I G N   K E Y   ( ` r e q u e s t e r U s e r I d ` )   R E F E R E N C E S   ` U s e r `   ( ` i d ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N ,  
     C O N S T R A I N T   ` f k _ R e v i e w R e q u e s t _ U s e r 2 `   F O R E I G N   K E Y   ( ` r e c e i v e r U s e r I d ` )   R E F E R E N C E S   ` U s e r `   ( ` i d ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N  
 )   E N G I N E = I n n o D B   A U T O _ I N C R E M E N T = 1 5   D E F A U L T   C H A R S E T = u t f 8   C O L L A T E = u t f 8 _ u n i c o d e _ c i ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   @ s a v e d _ c s _ c l i e n t   * / ;  
  
 - -  
 - -   T a b l e   s t r u c t u r e   f o r   t a b l e   ` R o l e `  
 - -  
  
 D R O P   T A B L E   I F   E X I S T S   ` R o l e ` ;  
 / * ! 4 0 1 0 1   S E T   @ s a v e d _ c s _ c l i e n t           =   @ @ c h a r a c t e r _ s e t _ c l i e n t   * / ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   u t f 8   * / ;  
 C R E A T E   T A B L E   ` R o l e `   (  
     ` n a m e `   v a r c h a r ( 3 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   N O T   N U L L   D E F A U L T   ' ' ,  
     ` d e s c r i p t i o n `   v a r c h a r ( 6 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` c r e a t e d A t `   d a t e t i m e   N O T   N U L L ,  
     ` u p d a t e d A t `   d a t e t i m e   D E F A U L T   N U L L ,  
     ` c r e a t e d B y I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` u p d a t e d B y I d `   i n t ( 1 1 )   D E F A U L T   N U L L ,  
     P R I M A R Y   K E Y   ( ` n a m e ` ) ,  
     U N I Q U E   K E Y   ` n a m e _ U N I Q U E `   ( ` n a m e ` )  
 )   E N G I N E = I n n o D B   D E F A U L T   C H A R S E T = u t f 8   C O L L A T E = u t f 8 _ u n i c o d e _ c i ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   @ s a v e d _ c s _ c l i e n t   * / ;  
  
 - -  
 - -   T a b l e   s t r u c t u r e   f o r   t a b l e   ` R o l e P e r m i s s i o n `  
 - -  
  
 D R O P   T A B L E   I F   E X I S T S   ` R o l e P e r m i s s i o n ` ;  
 / * ! 4 0 1 0 1   S E T   @ s a v e d _ c s _ c l i e n t           =   @ @ c h a r a c t e r _ s e t _ c l i e n t   * / ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   u t f 8   * / ;  
 C R E A T E   T A B L E   ` R o l e P e r m i s s i o n `   (  
     ` r o l e N a m e `   v a r c h a r ( 3 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   N O T   N U L L   D E F A U L T   ' ' ,  
     ` p e r m i s s i o n N a m e `   v a r c h a r ( 3 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   N O T   N U L L   D E F A U L T   ' ' ,  
     ` c r e a t e d A t `   d a t e t i m e   N O T   N U L L ,  
     ` u p d a t e d A t `   d a t e t i m e   D E F A U L T   N U L L ,  
     ` c r e a t e d B y I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` u p d a t e d B y I d `   i n t ( 1 1 )   D E F A U L T   N U L L ,  
     P R I M A R Y   K E Y   ( ` r o l e N a m e ` , ` p e r m i s s i o n N a m e ` ) ,  
     K E Y   ` f k _ R o l e P e r m i s s i o n _ R o l e 1 _ i d x `   ( ` r o l e N a m e ` ) ,  
     K E Y   ` f k _ R o l e P e r m i s s i o n _ P e r m i s s i o n 1 _ i d x `   ( ` p e r m i s s i o n N a m e ` ) ,  
     C O N S T R A I N T   ` f k _ R o l e P e r m i s s i o n _ P e r m i s s i o n 1 `   F O R E I G N   K E Y   ( ` p e r m i s s i o n N a m e ` )   R E F E R E N C E S   ` P e r m i s s i o n `   ( ` n a m e ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N ,  
     C O N S T R A I N T   ` f k _ R o l e P e r m i s s i o n _ R o l e 1 `   F O R E I G N   K E Y   ( ` r o l e N a m e ` )   R E F E R E N C E S   ` R o l e `   ( ` n a m e ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N  
 )   E N G I N E = I n n o D B   D E F A U L T   C H A R S E T = u t f 8   C O L L A T E = u t f 8 _ u n i c o d e _ c i ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   @ s a v e d _ c s _ c l i e n t   * / ;  
  
 - -  
 - -   T a b l e   s t r u c t u r e   f o r   t a b l e   ` T e n a n t R e q u e s t `  
 - -  
  
 D R O P   T A B L E   I F   E X I S T S   ` T e n a n t R e q u e s t ` ;  
 / * ! 4 0 1 0 1   S E T   @ s a v e d _ c s _ c l i e n t           =   @ @ c h a r a c t e r _ s e t _ c l i e n t   * / ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   u t f 8   * / ;  
 C R E A T E   T A B L E   ` T e n a n t R e q u e s t `   (  
     ` i d `   i n t ( 1 1 )   N O T   N U L L   A U T O _ I N C R E M E N T ,  
     ` e m a i l `   v a r c h a r ( 6 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   N O T   N U L L ,  
     ` a m o u n t `   f l o a t   N O T   N U L L ,  
     ` m e s s a g e `   v a r c h a r ( 1 4 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` o w n e r U s e r I d `   i n t ( 1 1 )   D E F A U L T   N U L L ,  
     ` c r e a t e d A t `   d a t e t i m e   N O T   N U L L ,  
     ` u p d a t e d A t `   d a t e t i m e   D E F A U L T   N U L L ,  
     ` c r e a t e d B y I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` u p d a t e d B y I d `   i n t ( 1 1 )   D E F A U L T   N U L L ,  
     P R I M A R Y   K E Y   ( ` i d ` ) ,  
     K E Y   ` f k _ T e n a n t R e q u e s t s _ U s e r 1 _ i d x `   ( ` o w n e r U s e r I d ` ) ,  
     C O N S T R A I N T   ` f k _ T e n a n t R e q u e s t s _ U s e r 1 `   F O R E I G N   K E Y   ( ` o w n e r U s e r I d ` )   R E F E R E N C E S   ` U s e r `   ( ` i d ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N  
 )   E N G I N E = I n n o D B   D E F A U L T   C H A R S E T = u t f 8   C O L L A T E = u t f 8 _ u n i c o d e _ c i ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   @ s a v e d _ c s _ c l i e n t   * / ;  
  
 - -  
 - -   T a b l e   s t r u c t u r e   f o r   t a b l e   ` U s e r `  
 - -  
  
 D R O P   T A B L E   I F   E X I S T S   ` U s e r ` ;  
 / * ! 4 0 1 0 1   S E T   @ s a v e d _ c s _ c l i e n t           =   @ @ c h a r a c t e r _ s e t _ c l i e n t   * / ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   u t f 8   * / ;  
 C R E A T E   T A B L E   ` U s e r `   (  
     ` i d `   i n t ( 1 1 )   N O T   N U L L   A U T O _ I N C R E M E N T ,  
     ` u s e r n a m e `   v a r c h a r ( 1 5 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` p a s s w o r d `   v a r c h a r ( 4 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` f i r s t N a m e `   v a r c h a r ( 3 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` l a s t N a m e `   v a r c h a r ( 3 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` e m a i l `   v a r c h a r ( 6 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` t i m e Z o n e `   v a r c h a r ( 6 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` r o l e N a m e `   v a r c h a r ( 3 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` t y p e `   t i n y i n t ( 1 )   N O T   N U L L   C O M M E N T   ' 1   -   O w n e r ,   2   -   T e n a n t ,   3   -   S y s t e m   U s e r ' ,  
     ` s t a t u s `   t i n y i n t ( 1 )   N O T   N U L L   C O M M E N T   ' 1   -   A c t i v e ,   0   -   I n a c t i v e ' ,  
     ` f b I d `   v a r c h a r ( 3 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` f b A c c e s s T o k e n `   v a r c h a r ( 4 5 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` g p l u s I d `   v a r c h a r ( 1 5 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` g p l u s A c c e s s T o k e n `   v a r c h a r ( 4 5 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` l i n k e d I n I d `   v a r c h a r ( 1 5 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` l i n k e d I n A c c e s s T o k e n `   v a r c h a r ( 4 5 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` p h o n e `   v a r c h a r ( 2 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` u s e r T o k e n `   v a r c h a r ( 2 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L   C O M M E N T   ' U s e r   a c c e s s   t o k e n ' ,  
     ` c r e a t e d A t `   d a t e t i m e   N O T   N U L L ,  
     ` u p d a t e d A t `   d a t e t i m e   D E F A U L T   N U L L ,  
     ` c r e a t e d B y I d `   i n t ( 1 1 )   D E F A U L T   N U L L ,  
     ` u p d a t e d B y I d `   i n t ( 1 1 )   D E F A U L T   N U L L ,  
     ` b a n k A c c o u n t N o `   v a r c h a r ( 4 5 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` b a n k N a m e `   v a r c h a r ( 3 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` p r o f i l e I m a g e `   t i n y t e x t   C O L L A T E   u t f 8 _ u n i c o d e _ c i ,  
     ` l a n g u a g e `   v a r c h a r ( 3 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` i d I m a g e `   v a r c h a r ( 3 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` t a x F i l e `   v a r c h a r ( 3 0 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` d o b `   d a t e   D E F A U L T   N U L L   C O M M E N T   ' D a t e   o f   b i r t h ' ,  
     ` l a s t A c c e s s `   d a t e t i m e   D E F A U L T   N U L L   C O M M E N T   ' L a s t   a c c e s s   d a t e   t i m e ' ,  
     ` b a n k A c c o u n t N a m e `   v a r c h a r ( 6 4 )   C O L L A T E   u t f 8 _ u n i c o d e _ c i   D E F A U L T   N U L L ,  
     ` i b a n `   v a r c h a r ( 6 0 )   C H A R A C T E R   S E T   l a t i n 1   D E F A U L T   N U L L   C O M M E N T   ' I B A N   n u m b e r ' ,  
     ` s w i f t `   v a r c h a r ( 3 0 )   C H A R A C T E R   S E T   l a t i n 1   D E F A U L T   N U L L   C O M M E N T   ' S W I F T   c o d e ' ,  
     ` r a t i n g `   f l o a t   N O T   N U L L   D E F A U L T   ' 0 '   C O M M E N T   ' O v e r a l l   u s e r   r a t i n g ' ,  
     P R I M A R Y   K E Y   ( ` i d ` ) ,  
     U N I Q U E   K E Y   ` e m a i l _ U N I Q U E `   ( ` e m a i l ` ) ,  
     U N I Q U E   K E Y   ` u s e r n a m e _ U N I Q U E `   ( ` u s e r n a m e ` ) ,  
     K E Y   ` f k _ U s e r _ R o l e 1 _ i d x `   ( ` r o l e N a m e ` ) ,  
     C O N S T R A I N T   ` f k _ U s e r _ R o l e 1 `   F O R E I G N   K E Y   ( ` r o l e N a m e ` )   R E F E R E N C E S   ` R o l e `   ( ` n a m e ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N  
 )   E N G I N E = I n n o D B   A U T O _ I N C R E M E N T = 2 5   D E F A U L T   C H A R S E T = u t f 8   C O L L A T E = u t f 8 _ u n i c o d e _ c i ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   @ s a v e d _ c s _ c l i e n t   * / ;  
  
 - -  
 - -   T a b l e   s t r u c t u r e   f o r   t a b l e   ` U s e r R e v i e w `  
 - -  
  
 D R O P   T A B L E   I F   E X I S T S   ` U s e r R e v i e w ` ;  
 / * ! 4 0 1 0 1   S E T   @ s a v e d _ c s _ c l i e n t           =   @ @ c h a r a c t e r _ s e t _ c l i e n t   * / ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   u t f 8   * / ;  
 C R E A T E   T A B L E   ` U s e r R e v i e w `   (  
     ` i d `   i n t ( 1 1 )   N O T   N U L L   A U T O _ I N C R E M E N T ,  
     ` u s e r I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` r e v i e w e d U s e r I d `   i n t ( 1 1 )   N O T   N U L L ,  
     ` r a t i n g `   i n t ( 2 )   D E F A U L T   N U L L ,  
     ` t i t l e `   v a r c h a r ( 4 5 )   C H A R A C T E R   S E T   l a t i n 1   N O T   N U L L ,  
     ` c o m m e n t `   v a r c h a r ( 1 4 5 )   C H A R A C T E R   S E T   l a t i n 1   D E F A U L T   N U L L ,  
     ` c r e a t e d A t `   d a t e t i m e   D E F A U L T   N U L L ,  
     P R I M A R Y   K E Y   ( ` i d ` ) ,  
     K E Y   ` f k _ U s e r R e v i e w _ U s e r 1 _ i d x `   ( ` u s e r I d ` ) ,  
     K E Y   ` f k _ U s e r R e v i e w _ U s e r 2 _ i d x `   ( ` r e v i e w e d U s e r I d ` ) ,  
     C O N S T R A I N T   ` f k _ U s e r R e v i e w _ U s e r 1 `   F O R E I G N   K E Y   ( ` u s e r I d ` )   R E F E R E N C E S   ` U s e r `   ( ` i d ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N ,  
     C O N S T R A I N T   ` f k _ U s e r R e v i e w _ U s e r 2 `   F O R E I G N   K E Y   ( ` r e v i e w e d U s e r I d ` )   R E F E R E N C E S   ` U s e r `   ( ` i d ` )   O N   D E L E T E   N O   A C T I O N   O N   U P D A T E   N O   A C T I O N  
 )   E N G I N E = I n n o D B   A U T O _ I N C R E M E N T = 1 1   D E F A U L T   C H A R S E T = u t f 8   C O L L A T E = u t f 8 _ u n i c o d e _ c i ;  
 / * ! 4 0 1 0 1   S E T   c h a r a c t e r _ s e t _ c l i e n t   =   @ s a v e d _ c s _ c l i e n t   * / ;  
 / * ! 4 0 1 0 3   S E T   T I M E _ Z O N E = @ O L D _ T I M E _ Z O N E   * / ;  
  
 / * ! 4 0 1 0 1   S E T   S Q L _ M O D E = @ O L D _ S Q L _ M O D E   * / ;  
 / * ! 4 0 0 1 4   S E T   F O R E I G N _ K E Y _ C H E C K S = @ O L D _ F O R E I G N _ K E Y _ C H E C K S   * / ;  
 / * ! 4 0 0 1 4   S E T   U N I Q U E _ C H E C K S = @ O L D _ U N I Q U E _ C H E C K S   * / ;  
 / * ! 4 0 1 0 1   S E T   C H A R A C T E R _ S E T _ C L I E N T = @ O L D _ C H A R A C T E R _ S E T _ C L I E N T   * / ;  
 / * ! 4 0 1 0 1   S E T   C H A R A C T E R _ S E T _ R E S U L T S = @ O L D _ C H A R A C T E R _ S E T _ R E S U L T S   * / ;  
 / * ! 4 0 1 0 1   S E T   C O L L A T I O N _ C O N N E C T I O N = @ O L D _ C O L L A T I O N _ C O N N E C T I O N   * / ;  
 / * ! 4 0 1 1 1   S E T   S Q L _ N O T E S = @ O L D _ S Q L _ N O T E S   * / ;  
  
 - -   D u m p   c o m p l e t e d   o n   2 0 1 6 - 0 1 - 1 6   1 5 : 2 1 : 3 1  
 